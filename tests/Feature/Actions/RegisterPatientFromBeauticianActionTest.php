<?php

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Actions\RegisterPatientFromBeautician;

it('register patient from beautician action', function () {

    loginAsUserBeautician();

    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',
        
    ];
    $office = auth()->user()->offices->first();

    $patient = app(RegisterPatientFromBeautician::class)($data);
    
    expect($patient)->ide->toEqual('503600224');
    expect($office->patients->contains($patient))->toBeTrue();
    expect(auth()->user()->patients->contains($patient))->toBeTrue();

})->shouldHaveCalledActions([
  CreatePatient::class => [],
  //CreateUser::class => []
]);
