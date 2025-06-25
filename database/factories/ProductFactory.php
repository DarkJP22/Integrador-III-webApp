<?php

namespace Database\Factories;

use App\Office;
use App\Product;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'office_id' => Office::factory(),
            'type' => 'P',
            'code' => $this->faker->word(),
            'name' => $this->faker->name(),
            'quantity' => $this->faker->randomDigit(),
            'price' => $this->faker->randomFloat(),
            'priceWithTaxes' => $this->faker->randomFloat(),
            'taxesAmount' => $this->faker->randomFloat(),
            'exo' => $this->faker->randomNumber(1),
            'measure' => 'Unid',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'laboratory' => $this->faker->randomNumber(1),
            'is_servicio_medico' => $this->faker->randomNumber(1),
            'CodigoProductoHacienda' => $this->faker->word(),
            'CodigoMoneda' => 'CRC',
            'reference_commission' => $this->faker->randomNumber(1),
            'no_aplica_commission' => $this->faker->randomNumber(1),
            'description' => $this->faker->text(),
        ];
    }
}
