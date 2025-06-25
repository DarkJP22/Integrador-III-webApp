<?php

namespace App\Actions;

use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class CreateUser
{

    public function __invoke(array $data, Role $role, $ruleValidations = []): User
    {

        $data = Validator::validate($data, [
            'ide' => [
                'required', 'unique:users,ide',
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '01', 'digits:9'),
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '02', 'digits:10'),
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '04', 'digits:10'),
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '03',
                    'digits_between:11,12'),
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '00', 'nullable'),
                Rule::when(!isset($data['tipo_identificacion']), 'digits:9'),
            ],
            'name' => ['required'],
            'phone_number' => ['required', 'digits_between:8,15'],
            'password' => ['required'],
            'email' => ['sometimes', 'nullable', 'email', 'unique:users,email'],
            'phone_country_code' => ['sometimes', 'nullable'],
            'whatsapp_number' => ['sometimes', 'nullable'],
            'push_token' => ['sometimes', 'nullable'],
            ...$ruleValidations
        ]);

        $data['api_token'] = Str::random(50);

        return DB::transaction(function () use ($data, $role) {

            $user = User::create([
                ...$data,
                'password' => bcrypt($data['password'])
            ]);
            $user->assignRole($role);
            $user->update([
                'current_role_id' => $role->id
            ]);
            $user->assignSpeciality($data['speciality'] ?? []);

            return $user;
        });


    }

}
