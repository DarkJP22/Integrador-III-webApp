<?php

use App\Actions\RegisterPatient;
use App\User;

it('create a patient', function(){

    loginAsUserApi();

    $this->post('/api/patient/register', [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',
        'birth_date' => '1987-09-20',
        'province' => '05',
        'canton' => '01',
        'district' => '01',
        'gender' => 'm'
    ])->assertCreated();


})->shouldHaveCalledActions([
   RegisterPatient::class => [],
]);