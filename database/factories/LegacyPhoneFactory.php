<?php

namespace Database\Factories;

use App\Models\LegacyPhone;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyPhoneFactory extends Factory
{
    protected $model = LegacyPhone::class;

    public function definition(): array
    {
        return [
            'idpes' => fn () => LegacyPersonFactory::new()->create(),
            'tipo' => $this->faker->randomElement([1, 2, 3, 4]),
            'ddd' => $this->faker->numerify('9#'),
            'fone' => $this->faker->numerify('9#######'),
            'idpes_cad' => fn () => LegacyUserFactory::new()->current(),
            'idpes_rev' => fn () => LegacyUserFactory::new()->current(),
            'data_rev' => $this->faker->dateTime(),
        ];
    }
}
