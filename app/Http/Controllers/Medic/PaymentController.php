<?php

namespace App\Http\Controllers\Medic;

use App\ExpedientCode;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Plan;
use App\Income;
use App\Repositories\IncomeRepository;
use App\Http\Controllers\Controller;
use App\Notifications\NewSubscriptionPharmacyFacturacion;
use App\Notifications\PaymentConfirmation;
use App\Notifications\PaymentExpedientCodeConfirmation;
use App\Notifications\PaymentPharmacySubscription;
use App\Notifications\PaymentSubscriptionConfirmation;
use App\Office;
use App\Repositories\UserRepository;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PaymentController extends Controller
{
    public function __construct(IncomeRepository $incomeRepo, UserRepository $userRepo)
    {
        $this->middleware('auth');

        $this->incomeRepo = $incomeRepo;
        $this->userRepo = $userRepo;

        $this->acquirerId = config('services.pasarela.acquire_id'); //env('ACQUIRE_ID');
        $this->commerceId = config('services.pasarela.commerce_id'); //env('COMMERCE_ID');
        $this->mallId = config('services.pasarela.mall_id'); //env('MALL_ID');
        $this->purchaseCurrencyCode = config('services.pasarela.currency_code'); //env('CURRENCY_CODE');
        $this->terminalCode = config('services.pasarela.terminal_code'); //env('TERMINAL_CODE');
        $this->claveSHA2 = config('services.pasarela.clave_sha2'); //env('CLAVE_SHA2');

        $this->administrators = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrador');
        })->get();

    }

    public function create($id = null)
    {
        $user = auth()->user();

        if ($id) {
            $incomes = Income::where('id', $id)->where('user_id', auth()->id())->where(function ($query) {
                $query->where('type', 'M') // por cita atendida
                    ->orWhere('type', 'MS'); // por subscripcion de paquete
            })->where('paid', 0)->get();
        } else {
            $incomes = $user->monthlyCharge();
        }

        $amountTotal = $incomes->sum('amount');
        
        if(!$amountTotal > 0) return redirect('/');

        $incomesIds = $incomes->pluck('id')->implode(',');
        $description = $incomes->pluck('description')->implode(',');

        $purchaseOperationNumber = getUniqueNumber();
        $amount = fillZeroRightNumber($amountTotal);
        $purchaseCurrencyCode = config('services.pasarela.currency_code'); //env('CURRENCY_CODE');
        $purchaseVerification = getPurchaseVerfication($purchaseOperationNumber, $amount, $purchaseCurrencyCode);

        $medic_name = $user->name;
        $medic_email = $user->email;

        return view('medic.payments.create')->with(compact('incomes', 'purchaseOperationNumber', 'amount', 'amountTotal', 'purchaseOperationNumber', 'purchaseCurrencyCode', 'purchaseVerification', 'medic_name', 'medic_email', 'incomesIds', 'description'));
    }

    /**
     * Purchase VPOS Response
     * @param Request $request
     * @return $this
     */
    public function purchaseResponse()
    {
       
        //purchaseVerication que devuelve la Pasarela de Pagos
        if(config('services.pasarela.simulation_vpos')){
            $purchaseVericationVPOS2 =  '';
            \Log::info('purchaseVerication VPOS:  ---vacio por simulación');

        }else{
            $purchaseVericationVPOS2 = request('purchaseVerification');
            \Log::info('purchaseVerication VPOS: ' . $purchaseVericationVPOS2);
        }
        

        //purchaseVerication que genera el comercio
        $purchaseVericationComercio = openssl_digest(request('acquirerId') . request('idCommerce') . request('purchaseOperationNumber') . request('purchaseAmount') . request('purchaseCurrencyCode') . request('authorizationResult') . $this->claveSHA2, 'sha512');

        \Log::info('purchaseVerication Comercio: ' . $purchaseVericationComercio);

        if( auth()->user()->hasRole('medico') ){
            return $this->medicResponse($purchaseVericationVPOS2, $purchaseVericationComercio);
        }elseif( auth()->user()->hasRole('paciente') ){
          return $this->userResponse($purchaseVericationVPOS2, $purchaseVericationComercio);

        }elseif( auth()->user()->hasRole('clinica') ){
            return $this->clinicResponse($purchaseVericationVPOS2, $purchaseVericationComercio);
          
        }elseif( auth()->user()->hasRole('farmacia') ){
            return $this->pharmacyResponse($purchaseVericationVPOS2, $purchaseVericationComercio);
          }
        
    }



    public function medicResponse($purchaseVericationVPOS2, $purchaseVericationComercio)
    {
        $reserved3 = null; // compra de plan o subscripcion
        //Si ambos datos son iguales
        if ($purchaseVericationVPOS2 == $purchaseVericationComercio || $purchaseVericationVPOS2 == '') {
            $authorizationResult = request('authorizationResult');
            $authorizationCode = request('authorizationCode');
            $errorCode = request('errorCode');
            $errorMessage = request('errorMessage');
            $bin = request('bin');
            $brand = request('brand');
            $paymentReferenceCode = request('paymentReferenceCode');
            $reserved1 = request('reserved1');
            $reserved2 = request('reserved2'); // income id or ids or plan id
            $reserved3 = request('reserved3'); // compra de plan o subscripcion
            $reserved4 = request('reserved4'); // income id si es para cambio de subscripcion
            $reserved5 = request('reserved5'); // compra de subscripcion de centro medico por parte de medico
            $reserved6 = request('reserved6'); // office to convert to centro medico
            $reserved22 = request('reserved22');
            $reserved23 = request('reserved23');
            $purchaseOperationNumber = request('purchaseOperationNumber');
            $total = request('purchaseAmount') / 100;
            $income = null;
            $incomes = [];
            $adminCentroMedico = null;
            $plan = null;
            
            if ($authorizationResult == 00) {
               
                if ($reserved3 && $reserved3 == 1) { // 1 es la compra de una subscripcion

                    $plan = Plan::find($reserved2);

                    // if($reserved5 && $reserved5 == 1){ // 1 compra de subscripcion de centro medico por parte de medico
                    //   $office = Office::find($reserved6);
                    //   $adminCentroMedico = $this->createAccountCentroMedico($plan, $office);

                    //   // informamos via email su confirmacion de pago de una compra
                    //   if (auth()->user()->email) {
                    //         try {
        
                    //             auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
                                
                    //             if($adminCentroMedico){
                    //                 $adminCentroMedico->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
                    //             }
                               
                    //         } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                    //         {
                    //             \Log::error($e->getMessage());
                    //         }   
                        
        
                    //     }

                    // }else{ // comprar subscripcion usuario actual

                        if (!auth()->user()->subscription()->first()) {
                            auth()->user()->subscription()->create([
                                'plan_id' => $plan->id,
                                'cost' => $plan->cost,
                                'quantity' => $plan->quantity,
                                'ends_at' => Carbon::now()->addMonths($plan->quantity),
                                'purchase_operation_number' => $purchaseOperationNumber
                            ]);
                            
                            auth()->user()->fe = $plan->include_fe;
                            auth()->user()->save();
                            // informamos via email su confirmacion de pago de una compra
                            if (auth()->user()->email) {
                                try {
            
                                    auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
            
                                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                                {
                                    \Log::error($e->getMessage());
                                }   
                               
            
                            }
                        }

                   // }
                    
        
                    
                } elseif ($reserved3 && $reserved3 == 2) { // 2 -cambio de subscription
                    $plan = Plan::find($reserved2); //nueva subscription
                    $income = Income::find($reserved4);
        
                    $subscription = auth()->user()->subscription()->first();
                    $cost_plan_anterior = $subscription->cost;
        
                    if ($subscription->plan_id != $plan->id) {
                        $subscription->plan_id = $plan->id;
                        $subscription->cost = $plan->cost;
                        $subscription->quantity = $plan->quantity;
                        $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                        $subscription->purchase_operation_number = $purchaseOperationNumber;
                        $subscription->save();
                        
                        if($income){
                            $income->description = 'Cambio de plan de subscripcion de $' . $cost_plan_anterior . ' al ' . $plan->title;
                            $income->paid = 1;
                            $income->purchase_operation_number = $purchaseOperationNumber;
                            $income->amount = $plan->cost;
                            $income->subscription_cost = $plan->cost;
                            $income->save();
                        }

                        auth()->user()->fe = $plan->include_fe;
                        auth()->user()->save();
        
                        // informamos via email su confirmacion de pago de una compra
                        if (auth()->user()->email) {
                            try {
        
                                auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
                            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                            {
                                \Log::error($e->getMessage());
                            }  
                           
        
                        }
                      
                    }
                } else {
                    $incomesIds = explode(',', $reserved2);
        
                    $income = $this->incomeRepo->findById(trim($incomesIds[0]));
                    $incomes = Income::whereIn('id', $incomesIds)->get();
                    $description = $incomes->pluck('description')->implode(',');
        
                    $medic = $income->medic;
        
                    $subscription = auth()->user()->subscription()->first();
                    $plan = Plan::find($subscription->plan_id);
        
                    foreach ($incomes as $income) {
                        if ($income->type == 'MS') {
        
                            $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                            $subscription->save();
                        }
                        $income->paid = 1;
                        $income->purchase_operation_number = $purchaseOperationNumber;
                        $income->save();
                    }
                    
                    // informamos via email su confirmacion de pago
                    if (auth()->user()->email) {
                        try {
        
                            auth()->user()->notify(new PaymentConfirmation($incomes, $description, $purchaseOperationNumber, $total));
                        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                        {
                            \Log::error($e->getMessage());
                        }  
                        
        
                    }
                  
                }
                

                flash('Pago realizado con exito', 'success');
            }
            if ($authorizationResult == 01) {
                flash('La operación ha sido denegada en el Banco Emisor', 'error');
            }
            if ($authorizationResult == 05) {
                flash('La operación ha sido rechazada', 'error');
            }
        } else {
            \Log::info('Transacción Invalida. Los datos fueron alterados en el proceso de respuesta');
        }

        \Log::info('results of VPOS: ' . json_encode(request()->all()));

       

        if ($reserved3 && ($reserved3 == 1 || $reserved3 == 2)) {
            return view('medic.subscriptions.response')->with(compact('authorizationCode', 'total', 'authorizationResult', 'purchaseOperationNumber', 'errorCode', 'errorMessage', 'plan', 'adminCentroMedico', 'reserved3'));
        } else {
            return view('medic.payments.response')->with(compact('authorizationCode', 'total', 'authorizationResult', 'purchaseOperationNumber', 'errorCode', 'errorMessage', 'income', 'incomes'));
        }

      
         
       
       
       
    }

    public function userResponse($purchaseVericationVPOS2, $purchaseVericationComercio)
    {

        if ($purchaseVericationVPOS2 == $purchaseVericationComercio || $purchaseVericationVPOS2 == '') {
            $authorizationResult = request('authorizationResult');
            $authorizationCode = request('authorizationCode');
            $errorCode = request('errorCode');
            $errorMessage = request('errorMessage');
            $bin = request('bin');
            $brand = request('brand');
            $paymentReferenceCode = request('paymentReferenceCode');
            $reserved1 = request('reserved1');
            $reserved2 = request('reserved2'); // income id or ids or plan id
            $reserved3 = request('reserved3'); // compra de plan o subscripcion
            $reserved4 = request('reserved4'); // income id si es para cambio de subscripcion
            $reserved22 = request('reserved22');
            $reserved23 = request('reserved23');
            $purchaseOperationNumber = request('purchaseOperationNumber');
            $total = request('purchaseAmount') / 100;
            $description = 'Código de authorización para visualizar expediente';

            if ($authorizationResult == 00) {
               
                $expedientCode = ExpedientCode::generate();
                $expedientCode->update(['created_by_purchase' => 1, 'status' => 1]);
                $code = $expedientCode->code;
                if ($expedientCode) {
                    
                    $expedientCode->send(auth()->user());
                    // informamos via email su confirmacion de pago de una compra
                    if (auth()->user()->email) {
                        try {

                            auth()->user()->notify(new PaymentExpedientCodeConfirmation($code,  $description, $total, $purchaseOperationNumber));

                        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                        {
                            \Log::error($e->getMessage());
                        }   
                        

                    }
                }

                flash('Pago realizado con exito', 'success');
            }
            if ($authorizationResult == 01) {
                flash('La operación ha sido denegada en el Banco Emisor', 'error');
            }
            if ($authorizationResult == 05) {
                flash('La operación ha sido rechazada', 'error');
            }
        } else {
            \Log::info('Transacción Invalida. Los datos fueron alterados en el proceso de respuesta');
        }

        \Log::info('results of VPOS: ' . json_encode(request()->all()));

        \Log::info('mostrar vista');
        return view('user.responseExpedientCode')->with(compact('authorizationCode', 'total', 'authorizationResult', 'purchaseOperationNumber', 'errorCode', 'errorMessage', 'code','description'));
        
          
        
    }

    public function clinicResponse($purchaseVericationVPOS2, $purchaseVericationComercio)
    {
        $reserved3 = null; // compra de plan o subscripcion
        $authorizationResult = null;
        //Si ambos datos son iguales
        if ($purchaseVericationVPOS2 == $purchaseVericationComercio || $purchaseVericationVPOS2 == '') {
            $authorizationResult = request('authorizationResult');
            $authorizationCode = request('authorizationCode');
            $errorCode = request('errorCode');
            $errorMessage = request('errorMessage');
            $bin = request('bin');
            $brand = request('brand');
            $paymentReferenceCode = request('paymentReferenceCode');
            $reserved1 = request('reserved1');
            $reserved2 = request('reserved2'); // income id or ids or plan id
            $reserved3 = request('reserved3'); // compra de plan o subscripcion
            $reserved4 = request('reserved4'); // income id si es para cambio de subscripcion
            $reserved22 = request('reserved22');
            $reserved23 = request('reserved23');
            $purchaseOperationNumber = request('purchaseOperationNumber');
            $total = request('purchaseAmount') / 100;
            $income = null;
            $incomes = [];

            $plan = null;
            
            if ($authorizationResult == 00) {
               
                if ($reserved3 && $reserved3 == 1) { // 1 es la compra de una subscripcion

                    $plan = Plan::find($reserved2);

                    

                    if (!auth()->user()->subscription()->first()) {
                        auth()->user()->subscription()->create([
                            'plan_id' => $plan->id,
                            'cost' => $plan->cost,
                            'quantity' => $plan->quantity,
                            'ends_at' => Carbon::now()->addMonths($plan->quantity),
                            'purchase_operation_number' => $purchaseOperationNumber
                        ]);

                        auth()->user()->fe = $plan->include_fe;
                        auth()->user()->save();
                        // informamos via email su confirmacion de pago de una compra
                        if (auth()->user()->email) {
                            try {
        
                                auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
        
                            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                            {
                                \Log::error($e->getMessage());
                            }   
                            
        
                        }
                    }

                } elseif ($reserved3 && $reserved3 == 2) { // 2 -cambio de subscription
                    $plan = Plan::find($reserved2); //nueva subscription
                    
        
                    $subscription = auth()->user()->subscription()->first();
                    $cost_plan_anterior = $subscription->cost;
        
                    
                        $subscription->plan_id = $plan->id;
                        $subscription->cost = $plan->cost;
                        $subscription->quantity = $plan->quantity;
                        $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                        $subscription->purchase_operation_number = $purchaseOperationNumber;
                        $subscription->save();


                        $income = Income::where('user_id', auth()->id())->where(function ($query) {
                            $query->Where('type', 'MS'); // por subscripcion de paquete
                        })->where('paid', 0)->update(
                            [
                                'paid' => 1,
                                'purchase_operation_number' => $purchaseOperationNumber,
                                'description' => 'Realizó cambio de plan a '. $plan->title,
                                'subscription_cost' => $plan->cost
                            ]
                        );

                        auth()->user()->fe = $plan->include_fe;
                        auth()->user()->save();
        
        
                        // informamos via email su confirmacion de pago de una compra
                        if (auth()->user()->email) {
                            try {
        
                                auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
                            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                            {
                                \Log::error($e->getMessage());
                            }  
                           
        
                        }
                      
                    
                    }  else {
                        $incomesIds = explode(',', $reserved2);
            
                        $income = $this->incomeRepo->findById(trim($incomesIds[0]));
                        $incomes = Income::whereIn('id', $incomesIds)->get();
                        $description = $incomes->pluck('description')->implode(',');
            
                        $user = $income->medic;
            
                        $subscription = auth()->user()->subscription()->first();
                        $plan = Plan::find($subscription->plan_id);
            
                        foreach ($incomes as $income) {
                            if ($income->type == 'MS') {
            
                                $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                                $subscription->save();
                            }
                            $income->paid = 1;
                            $income->purchase_operation_number = $purchaseOperationNumber;
                            $income->save();
                        }
                        
                        // informamos via email su confirmacion de pago
                        if (auth()->user()->email) {
                            try {
            
                                auth()->user()->notify(new PaymentConfirmation($incomes, $description, $purchaseOperationNumber, $total));
                            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                            {
                                \Log::error($e->getMessage());
                            }  
                            
            
                        }
                      
                    }
    

                flash('Pago realizado con exito', 'success');
            }
            if ($authorizationResult == 01) {
                flash('La operación ha sido denegada en el Banco Emisor', 'error');
            }
            if ($authorizationResult == 05) {
                flash('La operación ha sido rechazada', 'error');
            }
        } else {
            \Log::info('Transacción Invalida. Los datos fueron alterados en el proceso de respuesta');
        }

        \Log::info('results of VPOS: ' . json_encode(request()->all()));

       
        if ($authorizationResult == 00 && $reserved3 && $reserved3 == 1) {
        
            return Redirect('/office/register');

        }elseif($reserved3 && $reserved3 == 2){
            return view('clinic.subscriptions.response')->with(compact('authorizationCode', 'total', 'authorizationResult', 'purchaseOperationNumber', 'errorCode', 'errorMessage', 'plan','reserved3'));
        }else{
            return view('clinic.payments.response')->with(compact('authorizationCode', 'total', 'authorizationResult', 'purchaseOperationNumber', 'errorCode', 'errorMessage', 'income', 'incomes'));
        }

      
         
       
       
       
    }

    public function pharmacyResponse($purchaseVericationVPOS2, $purchaseVericationComercio)
    {
        $reserved3 = null; // compra de plan o subscripcion
        $authorizationResult = null;
        //Si ambos datos son iguales
        if ($purchaseVericationVPOS2 == $purchaseVericationComercio || $purchaseVericationVPOS2 == '') {
            $authorizationResult = request('authorizationResult');
            $authorizationCode = request('authorizationCode');
            $errorCode = request('errorCode');
            $errorMessage = request('errorMessage');
            $bin = request('bin');
            $brand = request('brand');
            $paymentReferenceCode = request('paymentReferenceCode');
            $reserved1 = request('reserved1');
            $reserved2 = request('reserved2'); // income id or ids or plan id
            $reserved3 = request('reserved3'); // compra de plan o subscripcion
            $reserved4 = request('reserved4'); // income id si es para cambio de subscripcion
            $reserved22 = request('reserved22');
            $reserved23 = request('reserved23');
            $purchaseOperationNumber = request('purchaseOperationNumber');
            $total = request('purchaseAmount') / 100;
            $income = null;
            $incomes = [];

            $plan = null;
           
            if ($authorizationResult == 00) {
               
                if ($reserved3 && $reserved3 == 1) { // 1 es la compra de una subscripcion

                    $plan = Plan::find($reserved2);

                    

                    if (!auth()->user()->subscription()->first()) {
                        auth()->user()->subscription()->create([
                            'plan_id' => $plan->id,
                            'cost' => $plan->cost,
                            'quantity' => $plan->quantity,
                            'ends_at' => Carbon::now()->addMonths($plan->quantity),
                            'purchase_operation_number' => $purchaseOperationNumber
                        ]);

                        auth()->user()->fe = $plan->include_fe;
                        auth()->user()->save();
                        // informamos via email su confirmacion de pago de una compra
                        if (auth()->user()->email) {
                            try {
        
                                auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
        
                            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                            {
                                \Log::error($e->getMessage());
                            }   
                            
        
                        }
                    }

                } elseif ($reserved3 && $reserved3 == 2) { // 2 -cambio de subscription
                    $plan = Plan::find($reserved2); //nueva subscription
                    
        
                    $subscription = auth()->user()->subscription()->first();
                    $cost_plan_anterior = $subscription->cost;
        
                    
                        $subscription->plan_id = $plan->id;
                        $subscription->cost = $plan->cost;
                        $subscription->quantity = $plan->quantity;
                        $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                        $subscription->purchase_operation_number = $purchaseOperationNumber;
                        $subscription->save();

                        $income = Income::where('user_id', auth()->id())->where(function ($query) {
                            $query->Where('type', 'MS'); // por subscripcion de paquete
                        })->where('paid', 0)->update(
                            [
                                'paid' => 1,
                                'purchase_operation_number' => $purchaseOperationNumber,
                                'description' => 'Realizó cambio de plan a '. $plan->title,
                                'subscription_cost' => $plan->cost
                            ]
                        );

                        auth()->user()->fe = $plan->include_fe;
                        auth()->user()->save();
        
        
                        // informamos via email su confirmacion de pago de una compra
                        if (auth()->user()->email) {
                            try {
        
                                auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
                                auth()->user()->notify(new PaymentPharmacySubscription($plan, $purchaseOperationNumber));
                                Notification::send($this->administrators, new NewSubscriptionPharmacyFacturacion(auth()->user(), $purchaseOperationNumber));
                            } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                            {
                                \Log::error($e->getMessage());
                            }  
                           
        
                        }
                      
                    
                }  else {
                    $incomesIds = explode(',', $reserved2);
        
                    $income = $this->incomeRepo->findById(trim($incomesIds[0]));
                    $incomes = Income::whereIn('id', $incomesIds)->get();
                    $description = $incomes->pluck('description')->implode(',');
        
                    $user = $income->medic;
        
                    $subscription = auth()->user()->subscription()->first();
                    $plan = Plan::find($subscription->plan_id);
        
                    foreach ($incomes as $income) {
                        if ($income->type == 'MS') {
        
                            $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                            $subscription->save();
                        }
                        $income->paid = 1;
                        $income->purchase_operation_number = $purchaseOperationNumber;
                        $income->save();
                    }
                    
                    // informamos via email su confirmacion de pago
                    if (auth()->user()->email) {
                        try {
        
                            auth()->user()->notify(new PaymentConfirmation($incomes, $description, $purchaseOperationNumber, $total));
                        } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                        {
                            \Log::error($e->getMessage());
                        }  
                        
        
                    }
                  
                }

                flash('Pago realizado con exito', 'success');
            }
            if ($authorizationResult == 01) {
                flash('La operación ha sido denegada en el Banco Emisor', 'error');
            }
            if ($authorizationResult == 05) {
                flash('La operación ha sido rechazada', 'error');
            }
        } else {
            \Log::info('Transacción Invalida. Los datos fueron alterados en el proceso de respuesta');
        }

        \Log::info('results of VPOS: ' . json_encode(request()->all()));

  
        if ($authorizationResult == 00 && $reserved3 && $reserved3 == 1) {
           
            return Redirect('/pharmacy/registerform');
            
        }elseif($reserved3 && $reserved3 == 2){
            return view('pharmacy.subscriptions.response')->with(compact('authorizationCode', 'total', 'authorizationResult', 'purchaseOperationNumber', 'errorCode', 'errorMessage', 'plan','reserved3'));
        }else{
            return view('pharmacy.payments.response')->with(compact('authorizationCode', 'total', 'authorizationResult', 'purchaseOperationNumber', 'errorCode', 'errorMessage', 'income', 'incomes'));
        }
        

      
         
       
       
       
    }

    public function createAccountCentroMedico(Plan $plan, Office $office) // medicos a centro medico
    {
        $user = auth()->user();
 
         $this->validate(request(), [
             'email' => ['nullable', 'email', Rule::unique('users')]//'required|email|max:255|unique:patients',
         ]);
 
        //validamos que en users no hay email que va a registrase como paciente
        $data = [
             'name' =>  request('name') ? request('name') : 'admin.'. Str::slug($user->name, '.'),
             'email' => request('email') ? request('email') : Str::slug($user->name, '.').$user->id.'@cittacr.com',
             'password' => request('password') ? request('password') : $user->phone_number,
             'role' => Role::whereName('clinica')->first(),
             'phone_country_code' => request('phone_country_code') ? request('phone_country_code') : '+506',
             'phone_number' => request('phone_number') ? request('phone_number') : $user->phone_number
         ];
 
         $data['active'] = 1; // los medicos estan inactivos por defecto para revision
         $data['api_token'] = Str::random(50);
         $data['fe'] = 1;
 
         $admin = $this->userRepo->store($data);
 
         if($admin){
 
            
             $office->type = 2; // clinica privada (centro medico)
             $office->fe = 1;
             $office->save();
 
             $admin->offices()->save($office, ['obligado_tributario' => 'C', 'verified' => 1]); //admin clinica
             $user->offices()->updateExistingPivot($office->id, ['obligado_tributario' => 'C', 'verified' => 1]); // medico
 
             $user->assignAccount($admin); // se asigna cuenta de admin de clinica a medico
 
         
             if (!$admin->subscription()->first()) {
 
                 $admin->subscription()->create([
                     'plan_id' => $plan->id,
                     'cost' => $plan->cost,
                     'quantity' => $plan->quantity,
                     'ends_at' => Carbon::now()->addMonths($plan->quantity),
                     'purchase_operation_number' => request('purchaseOperationNumber')
                 ]);
                
             }
 
            
 
             return $admin;
         }
 
         

 
         return false;
 
 
    } 

    /**
     * Guardar consulta(cita)
     */
    public function store($id)
    {
        //$purchaseOperationNumber = $this->getUniqueNumber();
    
        $income = $this->incomeRepo->findById($id);
        $medic = $income->medic;
        //$income->paid = 1;
        //$income->save();

        $purchaseOperationNumber = str_pad($income->id, 9, '0', STR_PAD_LEFT);

      

        $plan = Plan::find($medic->subscription->plan_id);
        $subscription = $medic->subscription;

        $subscription->cost = $plan->cost;
        $subscription->quantity = $plan->quantity;
        $subscription->ends_at = Carbon::parse($income->period_to)->addMonths($plan->quantity);
        $subscription->save();

        return back();
    }

    /**
     * Guardar consulta(cita)
     */
    public function show($id)
    {
        $income = $this->incomeRepo->findById($id);
        $medic = $income->medic;

        $medicData = [];
        //if($medic->subscription){

        //$dateStart = Carbon::parse($income->period_from)->setTime(0,0,0);
        //$dateEnd = Carbon::parse($income->period_to)->setTime(0,0,0);
        $month = Carbon::parse($income->date)->subMonth()->month;
        $year = Carbon::parse($income->date)->subMonth()->year;

        $incomesAttented = $medic->incomes()->where('month', $month)->where('year', $year)->where('type', 'I')->get();

        $incomesPending = $medic->incomes()->where('month', $month)->where('year', $year)->where('type', 'P')->get();
        // $incomesAttented = $medic->incomes()->where([['date', '>=', $dateStart],
        //     ['date', '<=', $dateEnd]])->where('type','I');

        // $incomesPending = $medic->incomes()->where([['date', '>=', $dateStart],
        //     ['date', '<=', $dateEnd]])->where('type','P');

        $medicData = [
            'id' => $medic->id,
            'name' => $medic->name,
            'attented' => $incomesAttented->count(),
            'attented_amount' => $incomesAttented->sum('amount'),
            'pending' => $incomesPending->count(),
            'pending_amount' => $incomesPending->sum('amount'),
            'amountByAttended' => getAmountPerAppointmentAttended(),
            'month' => $month . ' - ' . $year,
        ];
        //  }

        return $medicData;
    }
}
