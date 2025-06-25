<?php

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Actions\RegisterPatientFromClinic;
use App\User;

it('register patient from clinic action', function () {

    loginAsUserClinic();

    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',
        
    ];
    $medic = User::factory()->create();
    $medic->assignRole(role('medico'));
    $office = auth()->user()->offices->first();
    $medic->offices()->attach($office);
    
    $patient = app(RegisterPatientFromClinic::class)($data);
 

    expect($patient)->ide->toEqual('503600224');
    expect($office->patients->contains($patient))->toBeTrue();
    expect($medic->patients->contains($patient))->toBeFalse();

})->shouldHaveCalledActions([
  CreatePatient::class => [],
 //CreateUser::class => []
]);
