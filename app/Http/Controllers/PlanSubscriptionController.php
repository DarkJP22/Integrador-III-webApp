<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;

class PlanSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    /**
     * Lista de todas las citas de un doctor sin paginar
     */
    public function index()
    {
        if (auth()->user()->hasRole('clinica')) {

            return Plan::where('for_clinic', 1)->get();
            
        }

        if (auth()->user()->hasRole('farmacia')) {
            
            return Plan::where('for_pharmacy', 1)->get();
        }

        if (auth()->user()->hasRole('medico')) {
            
            return Plan::where('for_medic', 1)->where('for_clinic', 0)->get();
        }
     
    }

}
