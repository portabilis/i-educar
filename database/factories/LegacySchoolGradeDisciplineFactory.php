<?php

namespace Database\Factories;

use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolGradeDisciplineFactory extends Factory
{
    protected $model = LegacySchoolGradeDiscipline::class;

    public function definition(): array
    {
        return [
            'ref_ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ref_ref_cod_serie' => fn () => LegacyGradeFactory::new()->create(),
            'ref_cod_disciplina' => fn () => LegacyDisciplineFactory::new()->create(),
            'ativo' => 1,
            'anos_letivos' => '{' . now()->year . '}',
        ];
    }
}
