<?php


use App\LabAppointmentRequest;
use App\LabVisit;
use App\Office;

it('get visits from selected lab', function () {
    loginAsUserApi();
    $lab2 = Office::factory()->create(['type' => 3]);
    $lab1 = Office::factory()->create(['type' => 3]);

    $visits = LabVisit::factory()->count(2)->create(['office_id' => $lab1->id]);
    $visits2 = LabVisit::factory()->count(3)->create(['office_id' => $lab2->id]);
    $this->get('/api/lab/visits?office_id=' . $lab1->id)
        ->assertOk()
        ->assertJsonCount(2);
});

it('register a visit', function () {

    loginAsUserApi();

    $lab2 = Office::factory()->create(['type' => 3]);
    $lab = Office::factory()->create(['type' => 3]);

    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'first_name' => 'Alonso Chavarria',
        'phone_number' => '12345678', //|unique:users',
        'birth_date' => '1987-02-21',
        'coords' => '9.9357,-84.0875',
        'gender' => 'm',
        'province' => '05',
        'canton' => 'Liberia',
        'district' => 'Liberia',
        'visit_location' => 'casa',
        'office_id' => $lab->id,
    ];
    $this->post('/api/lab/appointment-requests/register', $data)
        ->assertCreated();

    expect(LabAppointmentRequest::where('patient_ide', '503600224')->first())->not->toBeNull()
        ->and(LabAppointmentRequest::where('patient_ide', '503600224')->first()->visit_location)->toBe('casa')
        ->and(LabAppointmentRequest::where('patient_ide', '503600224')->first()->office_id)->toBe($lab->id);
});
