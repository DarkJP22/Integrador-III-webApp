<?php

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Actions\UpdatePatient;
use App\Patient;
use App\User;

it('create patient action', function () {

    loginAsUser();

    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',
        'birth_date' => '1987-09-20',
        'province' => '05',
        'canton' => '01',
        'district' => '01'
    ];
    $patient = (new CreatePatient())($data);
       
    expect($patient->ide)->toEqual('503600224');

    $this->assertDatabaseHas(Patient::class,[
        'ide' => '503600224',
        'first_name' => 'ALONSO CHAVARRIA',
    ]);

});

