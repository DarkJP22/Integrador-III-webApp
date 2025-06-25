<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Appointment;

class AppointmentPolicy
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
     * Determine whether the user can update the company.
     *
     * @param  \App\User  $user
     * @param  \App\Appointment  $appointment
     * @return mixed
     */
    public function create(User $user)
    {

        return $user->hasRole('medico') || $user->hasRole('asistente') || $user->hasRole('operador') || $user->hasRole('esteticista');
    }
    /**
     * Determine whether the user can update the company.
     *
     * @param  \App\User  $user
     * @param  \App\Appointment  $appointment
     * @return mixed
     */
    public function update(User $user, Appointment $appointment)
    {

        return ($user->id == $appointment->user_id || $user->id == $appointment->created_by);
    }

    public function showAuthorized(User $user, Appointment $appointment)
    {
        if(($user->id == $appointment->user_id || $user->id == $appointment->created_by)){
            return true;
        }
        
        return $user->patients()->where('authorization', 1)
                                ->where('authorized_at', '>=', $appointment->date)
                                ->exists();
       
    }
}
