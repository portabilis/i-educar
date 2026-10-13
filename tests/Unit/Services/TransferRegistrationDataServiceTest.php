<?php

use App\Exceptions\Transfer\StagesAreNotSame;
use App\Services\CopyAbsenceService;
use App\Services\CopyDescriptiveOpinionService;
use App\Services\CopyScoreService;
use App\Services\TransferRegistrationDataService;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // Duas matrículas do mesmo aluno, série e ano, em cursos sem calendário
    // padrão; a antiga saiu por transferência ainda não atendida.
    $this->registrations = function (int $newStages, int $oldStages): array {
        $student = LegacyStudentFactory::new()->create();
        $course = LegacyCourseFactory::new()->create(['padrao_ano_escolar' => 0]);
        $old = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_cod_curso' => $course, 'ano' => 2030, 'ativo' => 1]);
        $new = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ref_cod_curso' => $course, 'ref_ref_cod_serie' => $old->ref_ref_cod_serie, 'ano' => 2030, 'ativo' => 1]);
        LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $old, 'ref_cod_matricula_entrada' => null, 'ativo' => 1]);

        foreach ([[$new, $newStages], [$old, $oldStages]] as [$registration, $stages]) {
            $enrollment = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);
            for ($i = 1; $i <= $stages; $i++) {
                LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $enrollment->ref_cod_turma, 'sequencial' => $i]);
            }
        }

        return [$new->fresh(), $old->fresh()];
    };

    $this->absences = Mockery::mock(CopyAbsenceService::class);
    $this->scores = Mockery::mock(CopyScoreService::class);
    $this->opinions = Mockery::mock(CopyDescriptiveOpinionService::class);
    $this->service = new TransferRegistrationDataService($this->absences, $this->scores, $this->opinions);
});

test('copies the data of the transferred registration', function () {
    [$new, $old] = ($this->registrations)(2, 2);

    $this->absences->shouldReceive('copy')->once()->withArgs(fn ($to, $from) => $to->is($new) && $from->is($old));
    $this->scores->shouldReceive('copy')->once();
    $this->opinions->shouldReceive('copy')->once();

    $this->service->transferData($new);
});

test('refuses when the number of stages differs', function () {
    [$new] = ($this->registrations)(2, 1);

    $this->expectException(StagesAreNotSame::class);

    $this->service->transferData($new);
});

test('does nothing without a pending transfer', function () {
    $this->absences->shouldNotReceive('copy');

    $this->assertNull($this->service->transferData(LegacyRegistrationFactory::new()->create()));
});
