<?php

use App\Actions\UpdatePatientByAdmin;
use App\Patient;


it('update a patient', function () {

    loginAsUser();

    $patient = Patient::factory()->create(['first_name' => 'Alonso', 'phone_number' => '89679099']);

    $this->put('/general/patients/'. $patient->id, [
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '89679098',
    ])->assertStatus(302);

})->shouldHaveCalledActions([
    UpdatePatientByAdmin::class => [],
]);
