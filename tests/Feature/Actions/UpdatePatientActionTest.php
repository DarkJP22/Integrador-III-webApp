<?php

use App\Actions\CreatePatient;
use App\Actions\CreateUser;
use App\Actions\UpdatePatient;
use App\Patient;
use App\User;

it('update patient action', function () {

    loginAsUserApi();
    $patient = Patient::factory()->create(['first_name' => 'Alonso', 'phone_number' => '89679099']);
    $data = [
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',  
    ];
    $patient = (new UpdatePatient())($patient, $data);
       
    $patient->refresh();
    
    expect($patient->first_name)->toEqual('ALONSO CHAVARRIA');
    expect($patient->phone_number)->toEqual('89679098');

    $this->assertDatabaseHas(Patient::class,[
        'first_name' => 'ALONSO CHAVARRIA',
        'phone_number' => '89679098',
    ]);

});

