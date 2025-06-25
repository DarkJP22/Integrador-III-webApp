<?php

namespace App\Actions;

use App\Patient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdatePatientByAdmin
{

    public function __invoke(Patient $patient, array $data): Patient
    {

        $data = Validator::validate($data, [
            'ide' => [
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '01', ['digits:9', 'required', Rule::unique('patients')->ignore($patient->id)]),
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '02', ['digits:10', 'required', Rule::unique('patients')->ignore($patient->id)]),

                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '04', ['digits:10', 'required', Rule::unique('patients')->ignore($patient->id)]),

                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '03',
                    ['digits_between:11,12', 'required', Rule::unique('patients')->ignore($patient->id)]),
                Rule::when(isset($data['tipo_identificacion']) && $data['tipo_identificacion'] === '00', 'nullable'),

            ],
            'first_name' => ['required'],
            'phone_number' => ['sometimes', 'nullable', 'digits_between:8,15'],
            'phone_number_2' => ['sometimes', 'nullable', 'digits_between:8,15'],

            'tipo_identificacion' => ['sometimes', 'required'],
            'email' => ['sometimes', 'nullable', 'email'],
            'phone_country_code' => ['sometimes', 'required'],
            'phone_country_code_2' => ['sometimes', 'nullable'],
            'whatsapp_number' => ['sometimes', 'nullable'],
            'gender' => ['sometimes', 'nullable'],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'province' => ['sometimes', 'nullable'],
            'canton' => ['sometimes', 'nullable'],
            'district' => ['sometimes', 'nullable'],
            'city' => ['sometimes', 'nullable'],
            'address' => ['sometimes', 'nullable'],
            'conditions' => ['sometimes', 'nullable'],
            'complete_information' => ['sometimes', 'boolean'],

        ]);


        $patient->fill($data)->save();

        return $patient->fresh();


    }


}
