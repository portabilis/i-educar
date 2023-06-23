<?php

namespace Database\Factories;

use App\Models\LegacySchoolHistoryDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolHistoryDisciplineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolHistoryDiscipline::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $history = LegacySchoolHistoryFactory::new()->create();

        return [
            'ref_ref_cod_aluno' => $history->ref_cod_aluno,
            'sequencial' => 1,
            'ref_sequencial' => $history->sequencial,
            'nm_disciplina' => $this->faker->word(),
            'nota' => $this->faker->randomFloat(2, 0, 10),
            'faltas' => $this->faker->randomNumber(),
            'carga_horaria_disciplina' => $this->faker->randomNumber(),
            'dependencia' => false,
            'tipo_base' => $this->faker->randomElement([1, 2]),
        ];
    }
}
