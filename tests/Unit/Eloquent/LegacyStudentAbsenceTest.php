<?php

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentAbsence;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentAbsence::class, relations: [
    'absences' => LegacyGeneralAbsence::class,
    'generalAbsences' => LegacyGeneralAbsence::class,
    'absencesByDiscipline' => LegacyDisciplineAbsence::class,
    'registration' => LegacyRegistration::class,
]));

EloquentModelTestCase::registerModelTests();

test('is by discipline', function () {
    $model = LegacyStudentAbsenceFactory::new()->create([
        'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
    ]);
    $this->assertTrue($model->isByDiscipline());
});

test('is general', function () {
    $model = LegacyStudentAbsenceFactory::new()->create([
        'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::GERAL,
    ]);
    $this->assertTrue($model->isGeneral());
});

test('absences are by discipline when the rule is by discipline', function () {
    $model = LegacyStudentAbsenceFactory::new()->create([
        'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
    ]);
    LegacyDisciplineAbsenceFactory::new()->create(['falta_aluno_id' => $model]);

    $this->assertCount(1, $model->absences);
    $this->assertInstanceOf(LegacyDisciplineAbsence::class, $model->absences->first());
});
