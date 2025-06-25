<?php

namespace App\Policies;

use App\User;
use App\Receptor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceptorPolicy
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
     * Determine whether the user can view the receptor.
     *
     * @param  \App\User  $user
     * @param  \App\Receptor  $receptor
     * @return mixed
     */
    public function view(User $user, Receptor $receptor)
    {
        //
    }

    /**
     * Determine whether the user can create receptors.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the receptor.
     *
     * @param  \App\User  $user
     * @param  \App\Receptor  $receptor
     * @return mixed
     */
    public function update(User $user, Receptor $receptor)
    {
        //
    }

    /**
     * Determine whether the user can delete the receptor.
     *
     * @param  \App\User  $user
     * @param  \App\Receptor  $receptor
     * @return mixed
     */
    public function delete(User $user, Receptor $receptor)
    {
        $obligadoTributario = $user->getObligadoTributario();

        return $receptor->obligado_tributario_id == $obligadoTributario->id && !$receptor->sent_to_hacienda;
    }

    /**
     * Determine whether the user can restore the receptor.
     *
     * @param  \App\User  $user
     * @param  \App\Receptor  $receptor
     * @return mixed
     */
    public function restore(User $user, Receptor $receptor)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the receptor.
     *
     * @param  \App\User  $user
     * @param  \App\Receptor  $receptor
     * @return mixed
     */
    public function forceDelete(User $user, Receptor $receptor)
    {
        //
    }
}
