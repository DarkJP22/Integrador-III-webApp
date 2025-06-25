<?php

namespace App\Http\Controllers\Operator;

use Illuminate\Http\Request;
use App\Patient;
use App\PatientCode;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\User;
use App\Http\Controllers\Controller;

class PatientPermissionController extends Controller
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

    /**
     * Guardar paciente
     */
    public function store(Patient $patient, User $medic = null)
    {

        $userToAssign = ($medic) ? $medic : auth()->user();

        if(!$userToAssign->patients()->where('patients.id', $patient->id)->exists()){
            $userToAssign->patients()->attach($patient);
        }
     

        if (request()->wantsJson()) {
            return response([], 200);
        }
        

        flash('Paciente Agregado a tu lista', 'success');

        return Redirect()->back();
    }
}
