<?php

use App\Actions\GetAppointmentAndRequestsForCommission;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Office;
use App\Plan;
use App\Subscription;
use App\User;

it('get count fo appointments and request for commission',function(){
    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $user = User::factory()->create(['active' => 1]);
    $user->assignRole(role('paciente'));
    $office = Office::factory()->create();
    $plan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    $appointmentRequestInRange = AppointmentRequest::factory()->create([
        'medic_id' => $medic->id,
        'user_id' => $user->id,
        'patient_id' => $user->id,
        'office_id' => $office->id,
        'date' => now()->subDay(),
        'scheduled_date' => now(),
        'start' => now()->addMinutes(10),
        'end' => now()->addMinutes(15),
        'status' => AppointmentRequestStatus::SCHEDULED,
        'scheduled_at' => now()
    ]);

    $appointmentRequestOutRange = AppointmentRequest::factory()->create([
        'medic_id' => $medic->id,
        'user_id' => $user->id,
        'patient_id' => $user->id,
        'office_id' => $office->id,
        'date' => now()->subDay(),
        'scheduled_date' => now(),
        'start' => now()->addMinutes(10),
        'end' => now()->addMinutes(15),
        'status' => AppointmentRequestStatus::SCHEDULED,
        'scheduled_at' => now()->addMinutes(15)
    ]);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $plan->id,
        'cost' => $plan->cost,
        'quantity' => $plan->quantity,
        'ends_at' => now(),
        'previous_billing_date' => now()->subMonth(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free',
        'created_at' => now()->subMonth()
    ]);

    $data = (new GetAppointmentAndRequestsForCommission())->handle($medic, $plan, now()->subDay(), now());

    expect($data->scheduledAppointmentRequestsInRangeQuantity)->toBe(1)
        ->and($data->scheduledAppointmentRequestsOutRangeQuantity)->toBe(1)
        ->and($data->pendingAppointmentRequestsQuantity)->toBe(0)
        ->and($data->scheduledAppointmentsQuantity)->toBe(0)
        ->and($data->commission_discount)->toBe(0.15)
        ->and($data->cost_by_appointment)->toEqual(1000)
        ->and($data->commission_discount_val)->toEqual(150)
        ->and($data->cost_by_appointment_with_discount)->toEqual(850);

});
