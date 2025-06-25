<?php

namespace App\Http\Controllers\Pharmacy;

use Illuminate\Http\Request;
use App\Patient;
use App\PatientCode;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\User;

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

    /**
     * Guardar paciente
     */
    public function authorization(Patient $patient, User $medic = null)
    {

        $this->validate(request(), [

            'code' => 'required'


        ]);

        $code = PatientCode::where('code', request('code'))->where('created_at', '>', Carbon::now()->subHours(2))->first();

        if (!$code) {

            if (request()->wantsJson()) {
                return response(['message' => 'Codigo no existe o ha expirado'], 422);
            }

            throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);

            return back();

        }

        $patient = $code->patient;

        $userToAutho = ($medic) ? $medic : auth()->user();

        $userToAutho->patients()->updateExistingPivot($patient->id, ['authorization'=> 1]);

        \DB::table('patient_codes')->where('patient_id', $patient->id)->delete();


        if (request()->wantsJson()) {
            return response([], 200);
        }


        flash('Paciente Autorizado', 'success');

        return Redirect()->back();
    }

     /**
     * Guardar paciente
     */
    public function addauthorization(Patient $patient, User $medic = null)
    {

        $this->validate(request(), [

            'code' => 'required'


        ]);

        $code = PatientCode::where('code', request('code'))->where('created_at', '>', Carbon::now()->subHours(2))->first();

        if (!$code) {

            if (request()->wantsJson()) {
                return response(['message' => 'Codigo no existe o ha expirado'], 422);
            }

            throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);

            return back();

        }

        $userToAssign = ($medic) ? $medic : auth()->user();

        if(!$userToAssign->patients()->where('patients.id', $patient->id)->exists()){
            $userToAssign->patients()->attach($patient);
        }
        

        $patient = $code->patient;

        $userToAutho = ($medic) ? $medic : auth()->user();

        $userToAutho->patients()->updateExistingPivot($patient->id, ['authorization'=> 1]);

        \DB::table('patient_codes')->where('patient_id', $patient->id)->delete();


        if (request()->wantsJson()) {
            return response([], 200);
        }


        flash('Paciente Autorizado', 'success');

        return Redirect()->back();
    }
}
