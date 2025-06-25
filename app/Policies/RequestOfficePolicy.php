<?php

namespace App\Policies;

use App\User;
use App\RequestOffice;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestOfficePolicy
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
     * Determine whether the user can view the requestOffice.
     *
     * @param  \App\User  $user
     * @param  \App\RequestOffice  $requestOffice
     * @return mixed
     */
    public function view(User $user, RequestOffice $requestOffice)
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can create requestOffices.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can update the requestOffice.
     *
     * @param  \App\User  $user
     * @param  \App\RequestOffice  $requestOffice
     * @return mixed
     */
    public function update(User $user, RequestOffice $requestOffice)
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can delete the requestOffice.
     *
     * @param  \App\User  $user
     * @param  \App\RequestOffice  $requestOffice
     * @return mixed
     */
    public function delete(User $user, RequestOffice $requestOffice)
    {
        //
    }
}
