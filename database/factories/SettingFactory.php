<?php

namespace Database\Factories;

use App\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'option' => $this->faker->word(),
            'value' => $this->faker->word(),
        ];
    }
}
