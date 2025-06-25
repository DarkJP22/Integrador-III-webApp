<?php

namespace Database\Factories;

use App\Plan;
use App\Subscription;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'cost' => $this->faker->randomFloat(),
            'quantity' => $this->faker->randomNumber(),
            'ends_at' => Carbon::now(),
            'purchase_operation_number' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'previous_billing_date' => Carbon::now(),

            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
        ];
    }
}
