<?php

namespace App\Policies;

use App\User;
use App\ReviewApp;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewAppPolicy
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
     * Determine whether the user can view the reviewApp.
     *
     * @param  \App\User  $user
     * @param  \App\ReviewApp  $reviewApp
     * @return mixed
     */
    public function view(User $user, ReviewApp $reviewApp)
    {
        return $user->hasRole('administrador');
    }

    /**
     * Determine whether the user can create reviewApps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the reviewApp.
     *
     * @param  \App\User  $user
     * @param  \App\ReviewApp  $reviewApp
     * @return mixed
     */
    public function update(User $user, ReviewApp $reviewApp)
    {
        //
    }

    /**
     * Determine whether the user can delete the reviewApp.
     *
     * @param  \App\User  $user
     * @param  \App\ReviewApp  $reviewApp
     * @return mixed
     */
    public function delete(User $user, ReviewApp $reviewApp)
    {
        //
    }
}
