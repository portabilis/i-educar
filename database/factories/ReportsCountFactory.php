<?php

namespace Database\Factories;

use App\Models\ReportsCount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportsCount>
 */
class ReportsCountFactory extends Factory
{
    protected $model = ReportsCount::class;

    public function definition(): array
    {
        return [
            'render' => $this->faker->randomElement(['json', 'jasper', 'html']),
            'template' => $this->faker->randomElement(['template1', 'template2', 'template3']),
            'success' => $this->faker->boolean,
            'date' => now(),
        ];
    }
}
