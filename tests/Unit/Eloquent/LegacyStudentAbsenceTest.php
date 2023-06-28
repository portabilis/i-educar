<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentAbsence;
use Database\Factories\LegacyStudentAbsenceFactory;
use RegraAvaliacao_Model_TipoPresenca;
use Tests\EloquentTestCase;

class LegacyStudentAbsenceTest extends EloquentTestCase
{
    protected $relations = [
        'absences' => LegacyGeneralAbsence::class,
        'generalAbsences' => LegacyGeneralAbsence::class,
        'absencesByDiscipline' => LegacyDisciplineAbsence::class,
        'registration' => LegacyRegistration::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyStudentAbsence::class;
    }

    public function testIsByDiscipline(): void
    {
        $model = LegacyStudentAbsenceFactory::new()->create([
            'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        ]);
        $this->assertTrue($model->isByDiscipline());
    }

    public function testIsGeneral(): void
    {
        $model = LegacyStudentAbsenceFactory::new()->create([
            'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::GERAL,
        ]);
        $this->assertTrue($model->isGeneral());
    }
}
