<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\AffiliationPlan;

class AffiliationPlanPolicy
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
    public function view(User $user, AffiliationPlan $affiliationPlan)
    {
       
        // if($user->isAssistant()){

        //     // $assistantUser = \DB::table('assistants_users')->where('assistant_id', $user->id)->first();
        //     $office = $user->clinicsAssistants->first();

        //     return $affiliationPlan->office_id == $office->id;
        // }

        if ($user->isClinic()) {

            // $assistantUser = \DB::table('assistants_users')->where('assistant_id', $user->id)->first();
            $office = $user->offices->first();

            return $affiliationPlan->office_id == $office->id;
        }


        return $user->hasRole('clinica');
    }

    /**
     * Determine whether the user can create products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('clinica');
    }

    /**
     * Determine whether the user can update the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function update(User $user, AffiliationPlan $affiliationPlan)
    {
       
    }

    /**
     * Determine whether the user can delete the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function delete(User $user, AffiliationPlan $affiliationPlan)
    {
        //
    }
}
