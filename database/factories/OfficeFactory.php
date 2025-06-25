<?php

namespace Database\Factories;

use App\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\odel=Office>
 */
class OfficeFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Office::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => 2,
            'name' => $this->faker->word,
            'address' => $this->faker->address(),
            'province' => '05',
            'canton' => '01',
            'district' => '01',
            'active' => 1,
        ];
    }
}
