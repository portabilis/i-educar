<?php

namespace Database\Factories;

use App\Models\RegionalType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegionalType>
 */
class RegionalTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
