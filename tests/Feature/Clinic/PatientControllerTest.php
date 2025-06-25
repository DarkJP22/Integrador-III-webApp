<?php

use App\Actions\RegisterPatientFromClinic;

it('create a patient', function () {

    loginAsUserClinic();

    $this->postJson('/clinic/patients', [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
        'phone_country_code' => '+506',

    ])->assertCreated()
        ->assertJson([
            'ide' => '503600224',
        ]);
})->shouldHaveCalledActions([
    RegisterPatientFromClinic::class => [],
]);
