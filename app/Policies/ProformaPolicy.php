<?php

namespace App\Policies;

use App\Proforma;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProformaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any proformas.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->isCurrentRole('medico')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the proforma.
     *
     * @param  \App\User  $user
     * @param  \App\Proforma  $proforma
     * @return mixed
     */
    public function view(User $user, Proforma $proforma)
    {
        if($user->isAssistant()){

            // $assistantUser = \DB::table('assistants_users')->where('assistant_id', $user->id)->first();
            $office = $user->clinicsAssistants->first();

            return $proforma->office_id == $office->id;
        }

        if ($user->isClinic() || $user->isLab()) {

            // $assistantUser = \DB::table('assistants_users')->where('assistant_id', $user->id)->first();
            $office = $user->offices->first();

            return $proforma->office_id == $office->id;
        }


        return $proforma->user_id == $user->id || $proforma->created_by == $user->id;
    }

    /**
     * Determine whether the user can create proformas.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return ( $user->hasRole('medico') && ($user->belongsToCentroMedico() || $user->subscriptionPlanHasFe()) || $user->hasRole('clinica') || $user->hasRole('asistente') || $user->hasRole('laboratorio') );
    }

    /**
     * Determine whether the user can update the proforma.
     *
     * @param  \App\User  $user
     * @param  \App\Proforma  $proforma
     * @return mixed
     */
    public function update(User $user, Proforma $proforma)
    {
       //
    }

    /**
     * Determine whether the user can delete the proforma.
     *
     * @param  \App\User  $user
     * @param  \App\Proforma  $proforma
     * @return mixed
     */
    public function delete(User $user, Proforma $proforma)
    {
        //
    }

    /**
     * Determine whether the user can restore the proforma.
     *
     * @param  \App\User  $user
     * @param  \App\Proforma  $proforma
     * @return mixed
     */
    public function restore(User $user, Proforma $proforma)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the proforma.
     *
     * @param  \App\User  $user
     * @param  \App\Proforma  $proforma
     * @return mixed
     */
    public function forceDelete(User $user, Proforma $proforma)
    {
        //
    }
}
