<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineDependence;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineDependenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineDependence::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $schoolGradeDiscipline = LegacySchoolGradeDisciplineFactory::new()->create();

        return [
            'ref_cod_matricula' => fn () => LegacyRegistrationFactory::new()->create(),
            'ref_cod_disciplina' => $schoolGradeDiscipline->ref_cod_disciplina,
            'ref_cod_escola' => $schoolGradeDiscipline->ref_ref_cod_escola,
            'ref_cod_serie' => $schoolGradeDiscipline->ref_ref_cod_serie,
            'observacao' => $this->faker->text(200),
            'cod_disciplina_dependencia' => fn () => LegacyDisciplineFactory::new()->create(),
        ];
    }
}
