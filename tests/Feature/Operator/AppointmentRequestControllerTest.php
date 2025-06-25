<?php


use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Patient;
use App\User;

it('get all appointment requests', function () {
    loginAsUserOperator();

    $appointmentRequests = AppointmentRequest::factory()->count(5)->create();

    $response = $this->get('/operator/appointment-requests');

    $response->assertStatus(200)
        ->assertViewHas(['appointmentRequests', 'search', 'medics', 'statuses'])
        ->assertSee($appointmentRequests[0]->medic->name)
        ->assertSee($appointmentRequests[0]->medic->phone_number)
        ->assertSee($appointmentRequests[0]->patient->fullname)
        ->assertSee($appointmentRequests[0]->patient->phone_number)
        ->assertSee($appointmentRequests[0]->office->name)
        ->assertSee($appointmentRequests[0]->office->phone);

});

it('filter by medic', function () {
    loginAsUserOperator();
    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $medic2 = User::factory()->create(['active' => 1]);
    $medic2->assignRole(role('medico'));

    $appointmentRequestsMedic1 = AppointmentRequest::factory()->count(1)->for($medic, 'medic')->create();
    $appointmentRequestsMedic2 = AppointmentRequest::factory()->count(1)->for($medic2, 'medic')->create();

    $response = $this->get('/operator/appointment-requests?'.http_build_query(['medic' => $medic->id]));

    $response->assertStatus(200)
        ->assertViewHas('appointmentRequests')
        ->assertSee($appointmentRequestsMedic1[0]->medic->name);

    $this->assertCount(1, $response->viewData('appointmentRequests'));
});

it('filter by status', function () {
    loginAsUserOperator();

    $appointmentRequestsReserved = AppointmentRequest::factory()->count(1)->create([
        'status' => AppointmentRequestStatus::RESERVED
    ]);

    $appointmentRequestsScheduled = AppointmentRequest::factory()->count(2)->create([
        'status' => AppointmentRequestStatus::SCHEDULED
    ]);

    $response = $this->get('/operator/appointment-requests?'.http_build_query(['status[]' => AppointmentRequestStatus::RESERVED->value]));

    $response->assertStatus(200)
        ->assertViewHas('appointmentRequests')
        ->assertSee($appointmentRequestsReserved[0]->medic->name);

    $this->assertCount(1, $response->viewData('appointmentRequests'));

});

it('search by patient', function () {
    loginAsUserOperator();
    $patient = Patient::factory()->create();
    $patient2 = Patient::factory()->create();

    $appointmentRequestsPatient = AppointmentRequest::factory()->count(1)->for($patient, 'patient')->create();

    $appointmentRequestsPatient2 = AppointmentRequest::factory()->count(1)->for($patient2, 'patient')->create();

    $response = $this->get('/operator/appointment-requests?'.http_build_query(['q' => $patient->ide]));

    $response->assertStatus(200)
        ->assertViewHas('appointmentRequests')
        ->assertSee($appointmentRequestsPatient[0]->patient->fullname);

    $this->assertCount(1, $response->viewData('appointmentRequests'));

});

it('promedio de atencion', function () {
    loginAsUserOperator();

    $appointmentRequests = AppointmentRequest::factory()->count(5)->create([
        'status' => AppointmentRequestStatus::SCHEDULED,
        'created_at' => now(),
        'scheduled_at' => now()->addMinutes(10)
    ]);
    $appointmentRequests = AppointmentRequest::factory()->count(5)->create([
        'status' => AppointmentRequestStatus::SCHEDULED,
        'created_at' => now(),
        'scheduled_at' => now()->addMinutes(20)
    ]);

    $response = $this->get('/operator/appointment-requests');

    $response->assertStatus(200)
        ->assertViewHas(['averages']);

    $this->assertEquals(15, $response->viewData('averages')->average_response);

});
