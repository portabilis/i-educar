<?php

namespace Database\Factories;

use App\Models\EducacensoDegree;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducacensoDegreeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EducacensoDegree::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'curso_id' => $this->faker->uuid,
            'nome' => $this->faker->name(),
            'classe_id' => $this->faker->randomDigitNotZero(),
            'user_id' => fn () => LegacyUserFactory::new()->current(),
            'created_at' => now(),
            'grau_academico' => $this->faker->numberBetween(1, 5),
        ];
    }
}
