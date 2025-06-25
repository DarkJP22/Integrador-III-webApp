<?php

namespace App\Http\Controllers\Pharmacy;

use Illuminate\Http\Request;
use App\Plan;
use App\Income;
use App\Http\Controllers\Controller;
use App\Notifications\PaymentConfirmation;
use App\Notifications\PaymentPharmacySubscription;
use App\Notifications\PaymentSubscriptionConfirmation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        if(!auth()->user()->hasRole('farmacia')){
            return redirect('/');
        }


        $plans = Plan::where('for_pharmacy', 1)->get(); //centro medico


        return view('pharmacy.subscriptions.index', compact('plans'));
    }

    public function renew($id = null)
    {
        $user = auth()->user();

        if ($id) {
            $incomes = Income::where('id', $id)->where('user_id', auth()->id())->where(function ($query) {
                $query->where('type', 'MS'); // por subscripcion de paquete
            })->where('paid', 0)->get();
        } else {
            $incomes = $user->monthlyCharge();
        }

        if(!$incomes->count()){ return redirect('/');}

        $amountTotal = $incomes->sum('amount');
        $incomesIds = $incomes->pluck('id')->implode(',');
        $description = $incomes->pluck('description')->implode(',');

        $purchaseOperationNumber = getUniqueNumber();
        $amount = fillZeroRightNumber($amountTotal);
        $purchaseCurrencyCode = config('services.pasarela.currency_code'); //env('CURRENCY_CODE');
        $purchaseVerification = getPurchaseVerfication($purchaseOperationNumber, $amount, $purchaseCurrencyCode);

        $medic_name = $user->name;
        $medic_email = $user->email;

        return view('pharmacy.subscriptions.renew')->with(compact('incomes', 'purchaseOperationNumber', 'amount', 'amountTotal', 'purchaseOperationNumber', 'purchaseCurrencyCode', 'purchaseVerification', 'medic_name', 'medic_email', 'incomesIds', 'description'));
    }

    public function renewVoucher()
    {
        $this->validate(request(), [
            'voucher' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png'],
            'incomes' => ['required', 'array'],
            'purchase_operation_number' => ['required']
        ]);

        DB::transaction(function () {
            // save the file in storage
            $path = request()->file('voucher')->store('subscriptions', 's3');

            if (!$path) {
                throw new Exception("The file could not be saved.", 500);
            }

            $incomes = Income::whereIn('id', request('incomes'))->get();
            $description = $incomes->pluck('description')->implode(',');
            $total = $incomes->sum('amount');

            $subscription = auth()->user()->subscription()->first();
            $plan = Plan::find($subscription->plan_id);

            foreach ($incomes as $income) {
                if ($income->type == 'MS') {

                    $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                    $subscription->purchase_operation_number = request('purchase_operation_number');
                    $subscription->save();
                }
                $income->paid = 1;
                $income->purchase_operation_number = request('purchase_operation_number');
                $income->voucher = $path;
                $income->save();
            }

            // informamos via email su confirmacion de pago
            if (auth()->user()->email) {
                try {

                    auth()->user()->notify(new PaymentConfirmation($incomes, $description, request('purchase_operation_number'), $total));
                } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                {
                    \Log::error($e->getMessage());
                }
            }
        });


        flash('Comprobante Subido Correctamente', 'success');

        return redirect()->back();
    }
   
    /**
     * Guardar consulta(cita)
     */
    public function create(Plan $plan)
    {
        if (!auth()->user()->hasRole('farmacia')) {
            return redirect('/');
        }
        
        $user = auth()->user();
      
        //if ($user->subscription && $user->subscription->plan_id == $plan->id) return Redirect('/');
        if ($user->subscription) return Redirect('/');

        if (!Plan::where('for_pharmacy', 1)->get()->contains('id', $plan->id)) return Redirect('/');

        
        $newPlan = $plan;

        $amountTotal = $newPlan->cost;
        $description = $newPlan->title;

        $purchaseOperationNumber = getUniqueNumber();
        $amount = fillZeroRightNumber($amountTotal);
        $purchaseCurrencyCode = config('services.pasarela.currency_code'); //env('CURRENCY_CODE');
        $purchaseVerification = getPurchaseVerfication($purchaseOperationNumber, $amount, $purchaseCurrencyCode);

        $medic_name = $user->name;
        $medic_email = $user->email;

        $planBuyChange = 1; // 1 compra de plan

       
        // $user->fe = $newPlan->include_fe;
        // $user->save();
        
        return view('pharmacy.subscriptions.buy')->with(compact('newPlan', 'purchaseOperationNumber', 'amount', 'amountTotal', 'purchaseOperationNumber', 'purchaseCurrencyCode', 'purchaseVerification', 'medic_name', 'medic_email', 'description', 'planBuyChange'));
    }

    /**
     * Guardar consulta(cita)
     */
    public function edit(Plan $plan)
    {
        if (!auth()->user()->hasRole('farmacia')) {
            return redirect('/');
        }

        $user = auth()->user();

        // if ( !$user->expiredSubscription()->count() && $user->subscription->cost > 0) {
        //     flash('No puedes cambiar de subscripción hasta que finalices el periodo de la actual', 'danger');
        //     return redirect(route('pharmacyChangeAccountType'));
        // };

        if ($user->subscription && $user->subscription->plan_id == $plan->id) return Redirect('/');

        if (!Plan::where('for_pharmacy', 1)->get()->contains('id', $plan->id)) return Redirect('/');

        $newPlan = $plan;

        $amountTotal = $newPlan->cost;
        $description = $newPlan->title;

        $purchaseOperationNumber = getUniqueNumber();
        $amount = fillZeroRightNumber($amountTotal);
        $purchaseCurrencyCode = config('services.pasarela.currency_code'); //env('CURRENCY_CODE');
        $purchaseVerification = getPurchaseVerfication($purchaseOperationNumber, $amount, $purchaseCurrencyCode);

        $medic_name = $user->name;
        $medic_email = $user->email;

        $planBuyChange = 2; // 2 cambio de plan

    

        // $user->fe = $newPlan->include_fe;
        // $user->save();
       

        return view('pharmacy.subscriptions.change')->with(compact('newPlan', 'purchaseOperationNumber', 'amount', 'amountTotal', 'purchaseOperationNumber', 'purchaseCurrencyCode', 'purchaseVerification', 'medic_name', 'medic_email', 'description', 'planBuyChange'));
    }

    public function changeVoucher(Plan $plan)
    {
        $this->validate(request(), [
            'voucher' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png'],
            'income' => ['sometimes'],
            'purchase_operation_number' => ['required']
        ]);

        DB::transaction(function () use ($plan) {
            // save the file in storage
            $path = request()->file('voucher')->store('subscriptions', 's3');

            if (!$path) {
                throw new Exception("The file could not be saved.", 500);
            }


            $income = Income::find(request('income'));

            $subscription = auth()->user()->subscription()->first();
            $cost_plan_anterior = $subscription->cost;

            if ($subscription->plan_id != $plan->id) {
                $subscription->plan_id = $plan->id;
                $subscription->cost = $plan->cost;
                $subscription->quantity = $plan->quantity;
                $subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                $subscription->purchase_operation_number = request('purchase_operation_number');
                $subscription->save();

                if ($income) {
                    $income->description = 'Cambio de plan de subscripcion de $' . $cost_plan_anterior . ' al ' . $plan->title;
                    $income->paid = 1;
                    $income->purchase_operation_number = request('purchase_operation_number');
                    $income->amount = $plan->cost;
                    $income->subscription_cost = $plan->cost;
                    $income->voucher = $path;
                    $income->save();
                }else{
                    auth()->user()->incomes()->create([
                        'type' => 'MS',
                        'medic_type' => 'A',
                        'amount' => $plan->cost,
                        'subscription_cost' => $plan->cost,
                        'appointment_id' => 0,
                        'office_id' => 0,
                        'date' => Carbon::now()->toDateString(),
                        'month' => Carbon::now()->month,
                        'year' => Carbon::now()->year,
                        'period_from' => Carbon::now()->toDateString(),
                        'period_to' => Carbon::now()->toDateString(),
                        'description' => 'Cambio de plan de subscripcion de $' . $cost_plan_anterior . ' al ' . $plan->title,
                        'purchase_operation_number' => request('purchase_operation_number'),
                        'paid' => 1,
                        'voucher' => $path
                    ]);
                    
                }

                auth()->user()->fe = $plan->include_fe;
                auth()->user()->save();

                // informamos via email su confirmacion de pago de una compra
                if (auth()->user()->email) {
                    try {
                        auth()->user()->notify(new PaymentPharmacySubscription($plan, request('purchase_operation_number')));
                    } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                    {
                        \Log::error($e->getMessage());
                    }
                }
            }
        });


        flash('Comprobante Subido Correctamente', 'success');

        return redirect(route('clinicChangeAccountType'));
    }

    public function changeToFreeSubscription(Plan $plan)
    {
        if (!auth()->user()->hasRole('farmacia')) {
            return redirect('/');
        }

        $user = auth()->user();
        
        if( !$plan->cost > 0 ){ // si es gratis o coste 0

                $income = Income::where('user_id', auth()->id())->where(function ($query) {
                    $query->Where('type', 'MS'); // por subscripcion de paquete
                })->where('paid', 0)->update(['paid' => 1, 'purchase_operation_number' => 'free']);


                $purchaseOperationNumber = $plan->cost > 0 ? '---' : 'free';

                if($user->subscription){
                    $user->subscription->plan_id = $plan->id;
                    $user->subscription->cost = $plan->cost;
                    $user->subscription->quantity = $plan->quantity;
                    $user->subscription->ends_at = Carbon::now()->addMonths($plan->quantity);
                    $user->subscription->purchase_operation_number = $purchaseOperationNumber;
                    $user->subscription->save();

                }else{

                    $user->subscription()->create([
                        'plan_id' => $plan->id,
                        'cost' => $plan->cost,
                        'quantity' => $plan->quantity,
                        'ends_at' => Carbon::now()->addMonths($plan->quantity),
                        'purchase_operation_number' => $purchaseOperationNumber
                    ]);
                }
                
                 $user->fe = $plan->include_fe;
                 $user->save();

                // informamos via email su confirmacion de pago de una compra
                if (auth()->user()->email) {
                    try {

                        auth()->user()->notify(new PaymentSubscriptionConfirmation($plan, $purchaseOperationNumber));
                    } catch (TransportExceptionInterface $e)  //Swift_RfcComplianceException
                    {
                        \Log::error($e->getMessage());
                    }  
                

                }

            flash('Cambio de subscripción realizado correctamente', 'success');

          
        }

        return back();//redirect('/');
    }

    
}
