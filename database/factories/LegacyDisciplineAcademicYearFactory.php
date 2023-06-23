<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineAcademicYear;
use ComponenteSerie_Model_TipoNota;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineAcademicYearFactory extends Factory
{
    protected $model = LegacyDisciplineAcademicYear::class;

    public function definition(): array
    {
        return [
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'ano_escolar_id' => fn () => LegacyGradeFactory::new()->create(),
            'carga_horaria' => 40,
            'tipo_nota' => ComponenteSerie_Model_TipoNota::NUMERICA,
            'anos_letivos' => '{' . now()->year . '}',
            'hora_falta' => 45,
        ];
    }
}
