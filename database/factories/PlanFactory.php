<?php

namespace Database\Factories;

use App\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => 'Perfil Médico',
            'cost' => 0,
            'quantity' => 1,
            'for_medic' => 1
        ];
    }
}
