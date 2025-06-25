<?php

namespace Database\Factories;

use App\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Patient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ide' => $this->faker->unique()->randomNumber(9),
            'first_name' => $this->faker->firstName,
            'birth_date' => $this->faker->date(),
            'gender' => $this->faker->randomElement(array('m', 'f')),
            'phone_number' => $this->faker->phoneNumber,
            'phone_country_code' => '+506',
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'province' => $this->faker->state,
            'city' => $this->faker->city,
        ];
    }
}
