<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\AppointmentRequest>
 */
class AppointmentRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'patient_id' => \App\Patient::factory(),
            'office_id' => \App\Office::factory(),
            'user_id' => \App\User::factory(),
            'medic_id' => \App\User::factory(),
            'status' => \App\Enums\AppointmentRequestStatus::RESERVED->value,
            'date' => $this->faker->date(),
            'scheduled_date' => null,
            'start' => null,
            'end' => null,

        ];
    }
}
