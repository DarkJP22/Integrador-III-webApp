<?php

use App\GeneratePatientData;
use App\Jobs\CreateUserDownloadData;
use App\Notifications\AccountCanceled;
use App\Patient;
use App\User;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Routing\UrlGenerator;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

it('can cancel account', function () {

    Queue::fake();

    $user = loginAsUserApi();
    $user->assignRole(role('paciente'));

    expect(auth()->user()->active)->toBe(1)
        ->and(auth()->user()->cancel_at)->toBeNull();

    deleteJson('/api/account/cancel')
        ->assertStatus(204);

    expect(auth()->user()->fresh()->active)->toBe(0)
        ->and(auth()->user()->fresh()->cancel_at)->not->toBeNull();

    Queue::assertPushed(CreateUserDownloadData::class);


});

it('signed route is called and action is performed', function () {

    $user = loginAsUserApi();
    $user->assignRole(role('paciente'));

    $url = URL::signedRoute('user.download-data.show', ['user' => auth()->id()]);

    $response = $this->get($url);

    $response->assertOk();
});

it('When a user who is a patient of another user registers, he or she is unlinked from the previous user to become part of the new user.', function(){
    $patient = Patient::factory()->create(['ide' => '503600224']);
    $parentUser = User::factory()->create(['ide' => '503600225']);
    $parentUser->assignRole(role('paciente'));
    $parentUser->patients()->attach($patient->id);

    $data = [
        'tipo_identificacion' => '01',
        'ide' => '503600224',
        'name' => 'Alonso Chavarria',
        'email' => 'alonso@test.com',
        'phone_number' => '12345678',
        'phone_country_code' => '506',
        'password' => 'password', // 2MB Max
        'password_confirmation' => 'password',

    ];

    postJson('/api/user/register', $data)
        ->assertStatus(200);

    $this->assertDatabaseMissing('patient_user', [
        'user_id' => $parentUser->id,
        'patient_id' => $patient->id
    ]);

    $user = User::where('ide', '503600224')->first();

    $this->assertDatabaseHas('patient_user', [
        'user_id' => $user->id,
        'patient_id' => $patient->id,
        'authorization' => 1
    ]);
});

