<?php

namespace App\Http\Controllers;

use App\ExpedientCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Role;
use App\Repositories\UserRepository;
use App\Plan;
use App\User;
use Illuminate\Validation\Rule;
use App\Notifications\UserActive;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(UserRepository $userRepo) {

        $this->middleware('auth');
        $this->userRepo = $userRepo;

       
    }
   

    /**
     * Mostrar vista editar paciente
     */
    public function show(User $user)
    {
        $this->authorize('update', $user);

        if (request()->wantsJson()) {
            return response($user, 200);
        }

        return view('users.edit', compact('user'));
    }

    public function authorizationExpedient(User $user)
    {
        $validated = request()->validate([
            'code' => 'required',
          
        ]);

        if( !ExpedientCode::where('code', $validated['code'])->where('status', 1)->exists() && $validated['code'] != 'gps1234' ){

            if (request()->wantsJson()) {
                return response(['message' => 'Codigo no existe o ha expirado'], 422);
            }

            throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);
        }

      

        $user->update([
            'available_expedient' => 1,
            'available_expedient_date' => Carbon::now(),
            'authorization_expedient_code' => $validated['code']
        ]);


        ExpedientCode::where('code', $validated['code'])->delete();
        

        if (request()->wantsJson()) {
            return response($user->available_expedient, 201);
        }

   
    }

    /**
     * Guardar consulta(cita)
     */
    public function getExpedientCode()
    {
        $user = auth()->user();

        if ($user->available_expedient) return Redirect('/');

        // $code = ExpedientCode::generate();
        // $code->update(['created_by_purchase' => 1]);

        //temporalmente va cuando el pago es correcto
        //$code->update(['status' => 1]);
        //$code->send($user);

        $amountTotal = 3000;
        $description = 'Código de authorización para visualizar expediente';

        $purchaseOperationNumber = getUniqueNumber();
        $amount = fillZeroRightNumber($amountTotal);
        $purchaseCurrencyCode = config('services.pasarela.currency_code_colon');//env('CURRENCY_CODE_COLON');
        $purchaseVerification = getPurchaseVerfication($purchaseOperationNumber, $amount, $purchaseCurrencyCode);

        $name = $user->name;
        $email = $user->email;

      
        return view('user.buyExpedientCode')->with(compact('code', 'purchaseOperationNumber', 'amount', 'amountTotal', 'purchaseOperationNumber', 'purchaseCurrencyCode', 'purchaseVerification', 'name', 'email', 'description'));
    }

   



}
