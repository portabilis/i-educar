<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolGradeFactory extends Factory
{
    protected $model = LegacySchoolGrade::class;

    public function definition(): array
    {
        return [
            'ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ref_cod_serie' => fn () => LegacyGradeFactory::new()->create(),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'anos_letivos' => '{' . now()->format('Y') . '}',
        ];
    }

    public function withDisciplines(): static
    {
        return $this->afterCreating(function (LegacySchoolGrade $schoolGrade) {
            $schoolGrade->grade->allDisciplines->each(fn (LegacyDisciplineAcademicYear $discipline) => LegacySchoolGradeDisciplineFactory::new()->create([
                'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
                'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
                'ref_cod_disciplina' => $discipline->componente_curricular_id,
                'anos_letivos' => $schoolGrade->anos_letivos,
            ]));
        });
    }
}
