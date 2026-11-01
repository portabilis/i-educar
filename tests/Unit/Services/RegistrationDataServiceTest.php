<?php

use App\Exceptions\Transfer\StagesAreNotSame;
use App\Services\CopyAbsenceService;
use App\Services\CopyDescriptiveOpinionService;
use App\Services\CopyScoreService;
use App\Services\RegistrationDataService;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // A matrícula nova conta as etapas do ano letivo da escola; a antiga, as
    // etapas da turma (curso sem calendário padrão).
    $this->registrations = function (int $newStages, int $oldStages): array {
        $academicYear = LegacySchoolAcademicYearFactory::new()->create(['ano' => 2030]);
        for ($i = 1; $i <= $newStages; $i++) {
            LegacyAcademicYearStageFactory::new()->create(['escola_ano_letivo_id' => $academicYear, 'sequencial' => $i]);
        }
        $new = LegacyRegistrationFactory::new()->create(['ref_ref_cod_escola' => $academicYear->ref_cod_escola, 'ano' => 2030]);

        $old = LegacyRegistrationFactory::new()->create(['ref_cod_curso' => LegacyCourseFactory::new()->create(['padrao_ano_escolar' => 0])]);
        $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $old, 'ativo' => 1]);
        for ($i = 1; $i <= $oldStages; $i++) {
            LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $enrollment->ref_cod_turma, 'sequencial' => $i]);
        }

        return [$new->fresh(), $old->fresh()];
    };

    $this->absences = Mockery::mock(CopyAbsenceService::class);
    $this->scores = Mockery::mock(CopyScoreService::class);
    $this->opinions = Mockery::mock(CopyDescriptiveOpinionService::class);
    $this->service = new RegistrationDataService($this->absences, $this->scores, $this->opinions);
});

test('copies absences opinions and scores when the stages match', function () {
    [$new, $old] = ($this->registrations)(2, 2);

    $this->absences->shouldReceive('copy')->once()->with($new, $old);
    $this->opinions->shouldReceive('copy')->once()->with($new, $old);
    $this->scores->shouldReceive('copy')->once()->with($new, $old);

    $this->service->copy($new, $old);
});

test('refuses when the number of stages differs', function () {
    [$new, $old] = ($this->registrations)(2, 1);

    $this->absences->shouldNotReceive('copy');

    $this->expectException(StagesAreNotSame::class);

    $this->service->copy($new, $old);
});
