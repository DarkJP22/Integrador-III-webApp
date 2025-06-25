<?php

namespace App\Actions;

use App\AppointmentRequest;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateAppointmentRequest
{

    public function __invoke(User $medic, User $user, array $data): AppointmentRequest
    {

        $data = Validator::validate($data, [
            'date' => ['nullable', 'date'],
            'patient_id' => ['required'],
            'office_id' => ['required']
        ]);

        return DB::transaction(function () use ($user, $medic, $data) {

            return AppointmentRequest::create([
                'user_id' => $user->id,
                'medic_id' => $medic->id,
                'office_id' => $data['office_id'],
                'patient_id' => $data['patient_id'],
                'date' => $data['date'] ?? now(),
            ]);
        });
    }
}
