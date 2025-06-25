<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'patient_id' => \App\Patient::factory(),
            'office_id' => \App\Office::factory(),
            'user_id' => \App\User::factory(),
            'status' => \App\Enums\AppointmentStatus::SCHEDULED->value,
            'date' => $this->faker->date(),
            'backgroundColor' => '#f3f3f3',
            'borderColor' => '#f3f3f3',
            'start' => null,
            'end' => null,
        ];
    }
}
