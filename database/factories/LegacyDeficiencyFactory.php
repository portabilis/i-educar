<?php

namespace Database\Factories;

use App\Models\LegacyDeficiency;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDeficiencyFactory extends Factory
{
    protected $model = LegacyDeficiency::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'educacenso' => $this->faker->randomNumber(1, 10),
            'disregards_different_rule' => $this->faker->boolean(),
            'require_medical_report' => $this->faker->boolean(),
        ];
    }
}
