<?php

namespace Database\Factories;

use App\Models\LegacyStudentProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyStudentProject>
 */
class LegacyStudentProjectFactory extends Factory
{
    protected $model = LegacyStudentProject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_cod_projeto' => fn () => LegacyProjectFactory::new(),
            'ref_cod_aluno' => fn () => LegacyStudentFactory::new(),
            'turno' => $this->faker->numberBetween(1, 4),
            'data_inclusao' => now(),
            'data_desligamento' => now(),
        ];
    }
}
