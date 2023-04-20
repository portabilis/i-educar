<?php

namespace Database\Factories;

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
}
