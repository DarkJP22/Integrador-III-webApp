<?php

use App\Actions\CreateSubscriptionInvoice;
use App\AppointmentRequest;
use App\Enums\AppointmentRequestStatus;
use App\Enums\SubscriptionInvoicePaidStatus;
use App\Office;
use App\Plan;
use App\Setting;
use App\Subscription;
use App\SubscriptionInvoice;
use App\User;


it('create Subscription invoice action', function () {
    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $plan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $plan->id,
        'cost' => $plan->cost,
        'quantity' => $plan->quantity,
        'ends_at' => now(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free',
    ]);



    (new CreateSubscriptionInvoice())($subscription, now()->subDay(), now());

    $this->assertDatabaseCount('subscription_invoices', 1);

    expect(SubscriptionInvoice::first()->customer_id)->toEqual($medic->id)
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and($subscription->previous_billing_date?->toDateString())->toBe(today()->toDateString())
        ->and($subscription->ends_at->toDateString())->toEqual(now()->addMonths($plan->quantity)->toDateString());

});

it('apply discount for subscriptionMonthsFree', function () {
    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $plan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    Setting::setSetting('subscription_months_free', 1);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $plan->id,
        'cost' => $plan->cost,
        'quantity' => $plan->quantity,
        'ends_at' => now(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
    ]);


    (new CreateSubscriptionInvoice())($subscription, now()->subDay(), now());

    $this->assertDatabaseCount('subscription_invoices', 1);

    expect(SubscriptionInvoice::first()->total)->toEqual(0)
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and(SubscriptionInvoice::first()->paid_status)->toEqual(SubscriptionInvoicePaidStatus::PAID)
        ->and(SubscriptionInvoice::first()->paid_at)->not->toBe(null);

});

it('not apply discount for subscriptionMonthsFree if the subscriptionMonthsFree is disabled ', function () {
    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $plan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    Setting::setSetting('subscription_months_free', 0);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $plan->id,
        'cost' => $plan->cost,
        'quantity' => $plan->quantity,
        'ends_at' => now(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free',
    ]);


    (new CreateSubscriptionInvoice())($subscription, now()->subDay(), now());

    $this->assertDatabaseCount('subscription_invoices', 1);

    expect(SubscriptionInvoice::first()->total)->toEqual(5000)
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and(SubscriptionInvoice::first()->paid_status)->toEqual(SubscriptionInvoicePaidStatus::UNPAID)
        ->and(SubscriptionInvoice::first()->paid_at)->toBe(null);

});

it('not apply discount for subscriptionMonthsFree if the subscription it not in the valid period ', function () {
    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $plan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    Setting::setSetting('subscription_months_free', 1);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $plan->id,
        'cost' => $plan->cost,
        'quantity' => $plan->quantity,
        'ends_at' => now(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free',
        'created_at' => now()->subMonth()
    ]);


    (new CreateSubscriptionInvoice())($subscription, now()->subDay(), now());

    $this->assertDatabaseCount('subscription_invoices', 1);

    expect(SubscriptionInvoice::first()->total)->toEqual(5000)
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and(SubscriptionInvoice::first()->paid_status)->toEqual(SubscriptionInvoicePaidStatus::UNPAID)
        ->and(SubscriptionInvoice::first()->paid_at)->toBe(null);

});

it('apply discount for subscriptionMonthsFree limit end of month', function () {
    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $plan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    Setting::setSetting('subscription_months_free', 2);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $plan->id,
        'cost' => $plan->cost,
        'quantity' => $plan->quantity,
        'ends_at' => now(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free',
        'created_at' => now()->subMonth()->endOfMonth()->setTime(0,0,0)
    ]);

    (new CreateSubscriptionInvoice())($subscription, now()->subDay(), now());

    $this->assertDatabaseCount('subscription_invoices', 1);

    expect(SubscriptionInvoice::first()->total)->toEqual(0)
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and(SubscriptionInvoice::first()->paid_status)->toEqual(SubscriptionInvoicePaidStatus::PAID)
        ->and(SubscriptionInvoice::first()->paid_at)->not->toBe(null);

});

it('calculate items discount for appointment requests in range', function () {
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


    (new CreateSubscriptionInvoice())($subscription, now()->subDay(), now());

    $this->assertDatabaseCount('subscription_invoices', 1);

    $itemDiscountVal = ($plan->commission_discount / 100) * $plan->general_cost_commission_by_appointment;
    $itemTotalInRange = $plan->general_cost_commission_by_appointment - $itemDiscountVal; // 1 appointment request 1000 - 150 = 850
    $itemTotalOutRange = $plan->general_cost_commission_by_appointment; // 1 appointment request 1000

    $invoiceTotal = ($plan->cost + $itemTotalInRange + $itemTotalOutRange); // 5000 + 850 + 1000 = 6850

    expect(SubscriptionInvoice::first()->total)->toEqual($invoiceTotal)
        ->and(SubscriptionInvoice::first()->discount_val)->toEqual($itemDiscountVal)
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas < 10 min')->discount)->toEqual($plan->commission_discount)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas < 10 min')->discount_val)->toEqual($itemDiscountVal)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas < 10 min')->total)->toEqual($itemTotalInRange)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas > 10 min')->discount)->toEqual(0)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas > 10 min')->discount_val)->toEqual(0)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas > 10 min')->total)->toEqual($itemTotalOutRange);

});