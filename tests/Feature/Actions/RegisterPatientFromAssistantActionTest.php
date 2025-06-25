<?php

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Actions\RegisterPatientFromAssistant;
use App\Actions\RegisterPatientFromClinic;


it('register patient from assistant action', function () {

  loginAsUserAssistant();

    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',
        
    ];

    $office = auth()->user()->clinicsAssistants->first();
    
    $patient = app(RegisterPatientFromAssistant::class)($data);
    
    expect($patient)->ide->toEqual('503600224');
    expect($office->patients->contains($patient))->toBeTrue();

})->shouldHaveCalledActions([
  CreatePatient::class => [],
  //CreateUser::class => []
]);
