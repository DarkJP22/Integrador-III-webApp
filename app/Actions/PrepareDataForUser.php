<?php

namespace App\Actions;

use App\GenerateMedicData;
use App\GeneratePatientData;
use App\Interfaces\GenerateDataDownload;
use App\User;

class PrepareDataForUser
{

    public function handle(User $user, string $folder = 'downloads-users'): GenerateDataDownload
    {
        return match (true) {
            $user->hasRole('paciente') => new GeneratePatientData($user, $folder),
            $user->hasRole('medico') => new GenerateMedicData($user, $folder),
            default => throw new \Exception('Unknown role')
        };

    }

}