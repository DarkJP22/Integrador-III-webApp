<?php

namespace App\Policies;

use App\Office;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfficePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->isCurrentRole('medico')) {
            return true;
        }

        return false;
    }

    public function view(User $user, Office $office): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Office $office): bool
    {
        return true;
    }

    public function delete(User $user, Office $office): bool
    {
        return true;
    }

    public function restore(User $user, Office $office): bool
    {
        return true;
    }

    public function forceDelete(User $user, Office $office): bool
    {
        return true;
    }
}
