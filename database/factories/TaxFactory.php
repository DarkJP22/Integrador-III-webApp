<?php

namespace Database\Factories;

use App\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaxFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tax::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => '01',
            'name' => 'IVA',
            'tarifa' => 13, // secret
            'CodigoTarifa' => '08'
        ];
    }
}
