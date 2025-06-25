<?php

use App\Actions\RegisterPatientFromPharmacy;

it('create a patient', function () {

    loginAsUserPharmacy();

    $this->postJson('/pharmacy/patients', [
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
    RegisterPatientFromPharmacy::class => [],
]);
