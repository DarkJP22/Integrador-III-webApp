<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipo_identificacion' => 'nullable',
            'ide' => 'nullable|unique:patients',
            'first_name' => 'required',
            'last_name' => 'nullable',
            'gender' => 'required',
            'phone_country_code' => 'required',
            'phone_number' => 'required|digits_between:8,15',
            'birth_date' => 'required',
            'email' => 'nullable|email',
            'province' => 'required',
            'conditions' => 'nullable',
            'account' =>'nullable|email|exists:users,email'

        ];
    }
}
