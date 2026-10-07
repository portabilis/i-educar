<?php

namespace Tests\Unit\Services;

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
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class RegistrationDataServiceTest extends TestCase
{
    use DatabaseTransactions;

    private MockInterface $absences;

    private MockInterface $scores;

    private MockInterface $opinions;

    private RegistrationDataService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->absences = Mockery::mock(CopyAbsenceService::class);
        $this->scores = Mockery::mock(CopyScoreService::class);
        $this->opinions = Mockery::mock(CopyDescriptiveOpinionService::class);
        $this->service = new RegistrationDataService($this->absences, $this->scores, $this->opinions);
    }

    // A matrícula nova conta as etapas do ano letivo da escola; a antiga, as
    // etapas da turma (curso sem calendário padrão).
    private function registrations(int $newStages, int $oldStages): array
    {
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
    }

    public function test_copies_absences_opinions_and_scores_when_the_stages_match(): void
    {
        [$new, $old] = $this->registrations(2, 2);

        $this->absences->shouldReceive('copy')->once()->with($new, $old);
        $this->opinions->shouldReceive('copy')->once()->with($new, $old);
        $this->scores->shouldReceive('copy')->once()->with($new, $old);

        $this->service->copy($new, $old);
    }

    public function test_refuses_when_the_number_of_stages_differs(): void
    {
        [$new, $old] = $this->registrations(2, 1);

        $this->absences->shouldNotReceive('copy');

        $this->expectException(StagesAreNotSame::class);

        $this->service->copy($new, $old);
    }
}
