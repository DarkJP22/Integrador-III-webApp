<?php

namespace App\Http\Controllers;

use App\Actions\CancelAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Log;
use App\User;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Can;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
      
       
    }

    public function switch(User $account)
    {
        if(!auth()->user()->hasAccount($account))
        {
            flash('No se realizó el cambio de cuenta', 'success');
            return Redirect('/');
        }

        \Auth::logout();

        \Auth::login($account);


        flash('Se realizó el cambio de cuenta correctamente', 'success');
        
        return Redirect('/');
    }

   
    public function destroy(CancelAccount $cancelAccount)
    {
        try {
            $cancelAccount->execute(request()->user());
        } catch (\Exception $e) {
            flash('No se pudo cancelar la cuenta: '. $e->getMessage(), 'danger');
        }

        auth()->logout();

        return Redirect('/');
    }

    

    
}
