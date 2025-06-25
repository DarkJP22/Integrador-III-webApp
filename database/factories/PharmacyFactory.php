<?php

namespace Database\Factories;

use App\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Pharmacy>
 */
class PharmacyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pharmacy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'address' => $this->faker->address(),
            'province' => '05',
            'canton' => '01',
            'district' => '01',
            'active' => 1,

        ];
    }
}
