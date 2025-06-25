<?php

use App\MedicineReminder;
use App\Pharmacy;
use App\Plan;
use App\Pmedicine;
use Carbon\Carbon;

it('shows medicine reminders from pharmacy', function () {

    $user = loginAsUserPharmacy();
    $plan = Plan::factory(['for_pharmacy' => 1, 'title' => 'Perfil Farmacia'])->create();

    $user->subscription()->create([
        'plan_id' => $plan->id,
        'cost' => $plan->cost,
        'quantity' => $plan->quantity,
        'ends_at' => Carbon::now()->addMonths($plan->quantity),
        'purchase_operation_number' => $plan->cost > 0 ? '---' : 'free'
    ]);


    $pharmacy1 = $user->pharmacies->first();
    $pharmacy2 = Pharmacy::factory()->create();


    $medicineReminder = MedicineReminder::create([
        'pharmacy_id' => $pharmacy1->id,
        'medicine_id' => 1,
        'name' => 'Medicamento 1',
        'date' => now()->toDateString(),
        'status' => \App\Enums\MedicineReminderStatus::NO_CONTACTED

    ]);
    $medicineReminder = MedicineReminder::create([
        'pharmacy_id' => $pharmacy2->id,
        'medicine_id' => 1,
        'name' => 'Medicamento 2',
        'date' => now()->toDateString(),
        'status' => \App\Enums\MedicineReminderStatus::NO_CONTACTED

    ]);

    $response = $this->get('/pharmacy/medicines/reminders?'.http_build_query(['start' => now()->toDateString()]));

    $response->assertOk();

    $response->assertSee("Medicamento 1");
    $response->assertDontSee("Medicamento 2");

});
