<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyStudentProject>
 */
class LegacyStudentProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_cod_aluno' => LegacyStudentFactory::new()->make(),
            'turno' => $this->faker->numberBetween(1, 4),
        ];
    }
}
