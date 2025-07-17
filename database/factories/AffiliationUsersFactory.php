<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\AffiliationUsers;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\AffiliationUsers>
 */
class AffiliationUsersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     protected $model = AffiliationUsers::class;
    public function definition(): array
    {
        return [
            'user_id' => \App\User::factory(),
            'date' => $this->faker->dateTime(),
            'active' => $this->faker->boolean(),
            'type_affiliation' => $this->faker->numberBetween(1, 3),
          'voucher' => "" . $this->faker->word . '.pdf',
        ];
    }
}
