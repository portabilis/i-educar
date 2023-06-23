<?php

namespace Database\Factories;

use App\Models\LegacyEducacensoStages;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyEducacensoStagesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyEducacensoStages::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->randomNumber(),
            'name' => $this->faker->name(),
        ];
    }
}
