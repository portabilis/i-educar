<?php

namespace Database\Factories;

use App\Models\LegacyPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyPersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyPerson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name,
            'data_cad' => now(),
            'tipo' => $this->faker->randomElement(['F', 'J']),
            'situacao' => $this->faker->randomElement(['A', 'I', 'P']),
            'origem_gravacao' => $this->faker->randomElement(['M', 'U', 'C', 'O']),
            'operacao' => $this->faker->randomElement(['I', 'A', 'E']),
        ];
    }
}
