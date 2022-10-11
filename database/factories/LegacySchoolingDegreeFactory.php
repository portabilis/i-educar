<?php

namespace Database\Factories;

use App\Models\LegacySchoolingDegree;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolingDegreeFactory extends Factory
{
    protected $model = LegacySchoolingDegree::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->randomDigitNotZero(),
            'description' => $this->faker->name,
            'schooling' => 1,
        ];
    }
}
