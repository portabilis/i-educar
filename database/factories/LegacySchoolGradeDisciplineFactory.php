<?php

namespace Database\Factories;

use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolGradeDisciplineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolGradeDiscipline::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $schoolGrade = LegacySchoolGradeFactory::new()->create();

        return [
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => fn () => LegacyDisciplineFactory::new()->create(),
            'ativo' => 1,
            'anos_letivos' => '{' . now()->year . '}',
        ];
    }
}
