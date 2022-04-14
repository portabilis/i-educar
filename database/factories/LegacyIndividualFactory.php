<?php

namespace Database\Factories;

use App\Models\LegacyIndividual;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyIndividualFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyIndividual::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'idpes' => LegacyPersonFactory::new()->create(),
            'data_cad' => now(),
            'operacao' => $this->faker->randomElement(['I', 'A', 'E']),
            'origem_gravacao' => $this->faker->randomElement(['M', 'U', 'C', 'O']),
        ];
    }
}
