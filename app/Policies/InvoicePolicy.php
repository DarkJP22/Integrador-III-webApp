<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Invoice;

class InvoicePolicy
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
     * Determine whether the user can view the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function view(User $user, Invoice $invoice)
    {
       
        if($user->isAssistant()){

            // $assistantUser = \DB::table('assistants_users')->where('assistant_id', $user->id)->first();
            $office = $user->clinicsAssistants->first();

            return $invoice->office_id == $office->id;
        }

        if ($user->isClinic() || $user->isLab()) {

            // $assistantUser = \DB::table('assistants_users')->where('assistant_id', $user->id)->first();
            $office = $user->offices->first();

            return $invoice->office_id == $office->id;
        }


        return $invoice->user_id == $user->id || $invoice->created_by == $user->id;
    }

    /**
     * Determine whether the user can create products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return ( $user->hasRole('medico') && ($user->permissionCentroMedico() || $user->subscriptionPlanHasFe()) || $user->hasRole('clinica') || $user->hasRole('asistente') || $user->hasRole('laboratorio') );
    }

    /**
     * Determine whether the user can update the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function update(User $user, Invoice $invoice)
    {
       
    }

    /**
     * Determine whether the user can delete the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function delete(User $user, Invoice $invoice)
    {
        //
    }
}
