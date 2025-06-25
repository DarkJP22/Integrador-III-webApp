<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Patient;

class PatientPolicy
{
    use HandlesAuthorization;


     /**
     * Determine whether the user can view any posts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }
    /**
     * Determine whether the user can create schedules.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('medico') || $user->hasRole('clinica') || $user->hasRole('asistente') || $user->hasRole('operador') || $user->hasRole('farmacia') || $user->hasRole('esteticista') || $user->hasRole('laboratorio');
    }

    /**
     * Determine whether the user can update the company.
     *
     * @param  \App\User  $user
     * @param  \App\Patient  $patient
     * @return mixed
     */
    public function update(User $user, Patient $patient)
    {
        if ($user->isAssistant()) {

            $office = $user->clinicsAssistants->first();

            return \DB::table('office_patient')->where('patient_id', $patient->id)->where('office_id', $office->id)->exists(); 
          
        }
        if ($user->isClinic()) {

            $office = $user->offices->first();

            return \DB::table('office_patient')->where('patient_id', $patient->id)->where('office_id', $office->id)->exists(); 

        }
        if ($user->isLab()) {

            return true; 

        }
        if ($user->isOperator()) {

            return true;

        }
        if ($user->isPharmacy()) { 

            $pharmacy = $user->pharmacies->first();

            if($pharmacy->id === 1) { // farmacia monserrat
                return true;
            }
       
            return \DB::table('patient_pharmacy')->where('patient_id', $patient->id)->where('pharmacy_id', $pharmacy->id)->exists(); 


        }
    
        return $user->hasPatient($patient->id);
    }
}
