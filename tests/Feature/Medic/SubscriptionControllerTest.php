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
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

it('change plan from detectable to basic without appointments (upload voucher)', function () {

    Setting::setSetting('subscription_months_free', 1);

    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $detectablePlan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    $basicPlan = Plan::factory()->create([
        'title' => 'Plan Básico',
        'cost' => 5000,
        'commission_by_appointment' => 0,
        'general_cost_commission_by_appointment' => 0,
        'specialist_cost_commission_by_appointment' => 0,
        'commission_discount' => 0,
        'commission_discount_range_in_minutes' => 0,
    ]);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $detectablePlan->id,
        'cost' => $detectablePlan->cost,
        'quantity' => $detectablePlan->quantity,
        'ends_at' => Carbon::now(),
        'purchase_operation_number' => $detectablePlan->cost > 0 ? '---' : 'free'
    ]);


    Storage::fake('s3');

    $file = UploadedFile::fake()->image('voucher.jpg');

    $response = $this->actingAs($medic)->post('/medic/subscriptions/'.$basicPlan->id.'/change-voucher', [
        'voucher' => $file,
        'purchase_operation_number' => '123456789',
    ]);

    $this->assertDatabaseCount('subscription_invoices', 1);

    Storage::disk('s3')->assertExists($file->hashName('subscription-invoices'));

    $subscription->refresh();

    expect(SubscriptionInvoice::first()->total)->toEqual(0)
        ->and(SubscriptionInvoice::first()->discount_val)->toEqual(5000)
        ->and(SubscriptionInvoice::first()->notes)->toEqual('Cambio de subscripción: '.$basicPlan->title)
        ->and(SubscriptionInvoice::first()->paid_status)->toEqual(SubscriptionInvoicePaidStatus::PAID)
        ->and(SubscriptionInvoice::first()->reference_number)->toEqual('123456789')
        ->and(SubscriptionInvoice::first()->comprobante)->toEqual('subscription-invoices/'.$file->hashName())
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and($subscription->purchase_operation_number)->toEqual('123456789')
        ->and($subscription->plan_id)->toBe($basicPlan->id)
        ->and($subscription->previous_billing_date?->toDateString())->toBe(now()->toDateString())
        ->and($subscription->ends_at->toDateString())->toEqual(now()->startOfMonth()->addMonths($basicPlan->quantity)->toDateString());

    $response->assertRedirect(route('medicChangeAccountType'));
});

it('change plan from detectable to basic with appointments (upload voucher)', function () {

    Setting::setSetting('subscription_months_free', 1);

    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $user = User::factory()->create(['active' => 1]);
    $user->assignRole(role('paciente'));
    $office = Office::factory()->create();
    $detectablePlan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    $basicPlan = Plan::factory()->create([
        'title' => 'Plan Básico',
        'cost' => 5000,
        'commission_by_appointment' => 0,
        'general_cost_commission_by_appointment' => 0,
        'specialist_cost_commission_by_appointment' => 0,
        'commission_discount' => 0,
        'commission_discount_range_in_minutes' => 0,
    ]);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $detectablePlan->id,
        'cost' => $detectablePlan->cost,
        'quantity' => $detectablePlan->quantity,
        'ends_at' => Carbon::now(),
        'previous_billing_date' => now()->subMonth(),
        'purchase_operation_number' => $detectablePlan->cost > 0 ? '---' : 'free'
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

    Storage::fake('subscriptions');

    $file = UploadedFile::fake()->image('voucher.jpg');

    $response = $this->actingAs($medic)->post('/medic/subscriptions/'.$basicPlan->id.'/change-voucher', [
        'voucher' => $file,
        'purchase_operation_number' => '123456789',
    ]);

    $this->assertDatabaseCount('subscription_invoices', 1);

    $itemDiscountVal = ($detectablePlan->commission_discount / 100) * $detectablePlan->general_cost_commission_by_appointment;
    $itemTotalInRange = $detectablePlan->general_cost_commission_by_appointment - $itemDiscountVal;
    $itemTotalOutRange = $detectablePlan->general_cost_commission_by_appointment;
    $discountPlan =  $detectablePlan->cost;
    $invoiceDiscountsTotal = $discountPlan + $itemDiscountVal;
    $invoiceTotal = (($detectablePlan->cost - $discountPlan) + $itemTotalInRange + $itemTotalOutRange);

    expect(SubscriptionInvoice::first()->total)->toEqual($invoiceTotal)
        ->and(SubscriptionInvoice::first()->discount_val)->toEqual($invoiceDiscountsTotal)
        ->and(SubscriptionInvoice::first()->notes)->toEqual('Cambio de subscripción: '.$basicPlan->title)
        ->and(SubscriptionInvoice::first()->paid_status)->toEqual(SubscriptionInvoicePaidStatus::CHECKING)
        ->and(SubscriptionInvoice::first()->reference_number)->toEqual('123456789')
        ->and(SubscriptionInvoice::first()->items)->toHaveCount(5)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas < 10 min')->discount)->toEqual($detectablePlan->commission_discount)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas < 10 min')->discount_val)->toEqual($itemDiscountVal)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas < 10 min')->total)->toEqual($itemTotalInRange)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas > 10 min')->discount)->toEqual(0)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas > 10 min')->discount_val)->toEqual(0)
        ->and(SubscriptionInvoice::first()->items->sole('name', 'Solicitudes de citas > 10 min')->total)->toEqual($itemTotalOutRange);

    $response->assertRedirect(route('medicChangeAccountType'));

})->shouldHaveCalledActions([
    CreateSubscriptionInvoice::class => [],
]);

it('change plan from basic to detectable', function () {

    $medic = User::factory()->create(['active' => 1]);
    $medic->assignRole(role('medico'));
    $user = User::factory()->create(['active' => 1]);
    $user->assignRole(role('paciente'));
    $office = Office::factory()->create();
    $detectablePlan = Plan::factory()->create([
        'title' => 'Plan Detectable',
        'cost' => 5000,
        'commission_by_appointment' => 1,
        'general_cost_commission_by_appointment' => 1000,
        'specialist_cost_commission_by_appointment' => 2500,
        'commission_discount' => 15,
        'commission_discount_range_in_minutes' => 10,
    ]);

    $basicPlan = Plan::factory()->create([
        'title' => 'Plan Básico',
        'cost' => 5000,
        'commission_by_appointment' => 0,
        'general_cost_commission_by_appointment' => 0,
        'specialist_cost_commission_by_appointment' => 0,
        'commission_discount' => 0,
        'commission_discount_range_in_minutes' => 0,
    ]);

    $subscription = Subscription::factory()->for($medic)->create([
        'plan_id' => $basicPlan->id,
        'cost' => $basicPlan->cost,
        'quantity' => $basicPlan->quantity,
        'ends_at' => Carbon::now(),
        'previous_billing_date' => now()->subMonth(),
        'purchase_operation_number' => $basicPlan->cost > 0 ? '---' : 'free'
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


    $response = $this->actingAs($medic)->post('/medic/subscriptions/'.$detectablePlan->id.'/change', [
        'purchase_operation_number' => '123456789',
    ]);

    $this->assertDatabaseCount('subscription_invoices', 0);

    $subscription->refresh();

    expect($subscription->purchase_operation_number)->toEqual('123456789')
        ->and($subscription->plan_id)->toBe($detectablePlan->id)
        ->and($subscription->previous_billing_date?->toDateString())->toBe(now()->subMonth()->toDateString())
        ->and($subscription->ends_at->toDateString())->toEqual(now()->startOfMonth()->addMonths($detectablePlan->quantity)->toDateString());


    $response->assertRedirect(route('medicChangeAccountType'));
});
