<?php

use App\Appointment;
use App\Enums\AppointmentStatus;
use App\Office;
use App\Patient;
use App\User;

it('Create Appointment', function ()
{
    loginAsUserClinic();

    $medic = User::factory()->create();
    $medic->assignRole(role('medico'));

    $patient = Patient::factory()->create();
    // $patient->assignRole(role('paciente'));

    $office = auth()->user()->offices->first();
    // office_info: (event.office_info) ? event.office_info : '',
    // allDay: 0,
    // room_id: event.room_id,
    // optreatment_ids: event.optreatment_ids,

    $data = [
        'title' => 'Cita de prueba',
        'date' => '2022-02-14',
        'start' => '2022-02-14T06:00:00',
        'end' => '2022-02-14T06:30:00',
        'backgroundColor' => '#f3f3f3',
        'borderColor' => '#f3f3f3',
        'office_id' => $office->id,
        'patient_id' => $patient->id,
        'user_id' => $medic->id
    ];

    $this->postJson('/appointments', $data)
        //->dump()
        ->assertCreated()
        ->assertJson([
            'title' => 'Cita de prueba',
        ]);

    expect($office->patients->contains($patient))->toBeTrue();
    expect($medic->patients->contains($patient))->toBeTrue();



});

it('can delete an appointment', function () {
    loginAsUserOperator();


    $medic = User::factory()->create();
    $medic->assignRole(role('medico'));
    $patient = Patient::factory()->create();
    $office = Office::factory()->create();

    $appointment = Appointment::factory()->create([
        'office_id' => $office->id,
        'patient_id' => $patient->id,
        'user_id' => $medic->id,
        'date' => '2022-02-14',
        'start' => '2022-02-14T06:00:00',
        'end' => '2022-02-14T06:30:00',
    ]);

    $this->deleteJson('/appointments/' . $appointment->id)
        ->assertStatus(204);

    expect(Appointment::find($appointment->id))->toBeNull();

});

it('cannot delete an appointment with status different to scheduled', function () {

    loginAsUserOperator();


    $medic = User::factory()->create();
    $medic->assignRole(role('medico'));
    $patient = Patient::factory()->create();
    $office = Office::factory()->create();

    $appointment = Appointment::factory()->create([
        'office_id' => $office->id,
        'patient_id' => $patient->id,
        'user_id' => $medic->id,
        'date' => '2022-02-14',
        'start' => '2022-02-14T06:00:00',
        'end' => '2022-02-14T06:30:00',
        'status' => AppointmentStatus::STARTED->value
    ]);

    $this->deleteJson('/appointments/' . $appointment->id)
        ->assertStatus(422);

    expect(Appointment::find($appointment->id))->not->toBeNull();
});