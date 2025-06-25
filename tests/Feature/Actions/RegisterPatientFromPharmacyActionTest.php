<?php

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Actions\RegisterPatientFromPharmacy;
use App\Pharmacy;
use App\User;

it('register patient from pharmacy action', function () {

    loginAsUserPharmacy();

    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',
        
    ];

    $patient = app(RegisterPatientFromPharmacy::class)($data);
       
    expect($patient)->ide->toEqual('503600224');
    //expect($patient->user->pluck('ide'))->toContain('503600224');

})->shouldHaveCalledActions([
  CreatePatient::class => [],
]);
