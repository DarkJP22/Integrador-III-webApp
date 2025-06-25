<?php

use App\Actions\CreateSubscriptionInvoice;
use App\Jobs\UserBillingJob;
use App\Plan;
use App\Subscription;
use App\User;
use Illuminate\Support\Carbon;

it('batch of medic billing jobs is pushed', function () {

    Bus::fake()->serializeAndRestore();

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
        'ends_at' => Carbon::now(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
    ]);


    \Illuminate\Support\Facades\Artisan::call('gps:subscriptionCharge');

    // Assert a job was pushed to a given queue...
    Bus::assertBatched(function (\Illuminate\Bus\PendingBatch $batch) {
        return //$batch->name == 'import-csv' &&
            $batch->jobs->count() === 1;
    });


});

it('medic billing', function () {
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
        'ends_at' => Carbon::now(),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
    ]);


    (new UserBillingJob($medic))->handle();


})->shouldHaveCalledActions([
    CreateSubscriptionInvoice::class => [],
]);

