<?php

namespace App\Actions;

use App\Patient;
use App\Role;
use Illuminate\Support\Facades\DB;

class RegisterPatientFromBeautician
{

    public function __construct(protected CreatePatient $createPatient, protected CreateUser $createUser)
    {
    }

    public function __invoke(array $data, $ruleValidations = []): Patient
    {
        return DB::transaction(function () use ($data, $ruleValidations) {

            $patient = ($this->createPatient)($data, $ruleValidations);

//            $user = ($this->createUser)([
//                ...$data,
//                'password' => (isset($data['password']) && $data['password']) ? $data['password'] : $data['phone_number'],
//                'name' => $data['first_name']
//            ], Role::where('name', 'paciente')->first());
//
//
//            $user->patients()->save($patient, ['authorization' => 1]); // se asigna a la cuenta del paciente

            $office = auth()->user()->offices->first();
            $office->patients()->save($patient, ['authorization' => 1]); //se asigna a la estetica

            foreach ($office->activeMedics as $medicBeautician) { //se agregan a todos los medicos de la estetica

                $medicBeautician->patients()->save($patient, ['authorization' => 1]);

            }

            return $patient;
        });
    }
}
