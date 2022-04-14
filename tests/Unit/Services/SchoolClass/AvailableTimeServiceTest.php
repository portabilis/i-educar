<?php

namespace Tests\Unit\Services\SchoolClass;

use App\Services\SchoolClass\AvailableTimeService;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStudentFactory;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AvailableTimeServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var AvailableTimeService
     */
    private $service;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(AvailableTimeService::class);
        $this->disableForeignKeys();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->enableForeignKeys();

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testWithoutOthersEnrollmentsReturnsTrue()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $registration = LegacyRegistrationFactory::new()->create();

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    /**
     * @return void
     */
    public function testWithEnrollmentsSameDayDifferentTimeReturnsTrue()
    {
        $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $otherSchoolClass = LegacySchoolClassFactory::new()->afternoon()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $registration = LegacyRegistrationFactory::new()->create();

        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $otherSchoolClass,
        ]);

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    /**
     * @return void
     */
    public function testWithEnrollmentsSameDaySameTimeSameYearReturnsFalse()
    {
        $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $registration = LegacyRegistrationFactory::new()->create(['ano' => $schoolClass->ano, 'aprovado' => 3]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $otherSchoolClass,
        ]);

        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        $this->assertFalse($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    /**
     * @return void
     */
    public function testWithEnrollmentsSameDaySameTimeDifferentYearReturnsTrue()
    {
        $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $registration = LegacyRegistrationFactory::new()->create(['ano' => ($schoolClass->ano - 1)]);

        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    /**
     * @return void
     */
    public function testWithEnrollmentsDifferentDaySameTimeReturnsFalse()
    {
        $schoolClass = LegacySchoolClassFactory::new()->morning()->create([
            'tipo_mediacao_didatico_pedagogico' => 1,
            'dias_semana' => '{1, 7}',
        ]);
        $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $registration = LegacyRegistrationFactory::new()->create();

        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    /**
     * @return void
     */
    public function testShouldLaunchExceptionWhenPassInvalidSchoolClassId()
    {
        $this->expectException(ModelNotFoundException::class);

        $registration = LegacyRegistrationFactory::new()->create();

        $this->service->isAvailable($registration->ref_cod_aluno, -1);
    }

    /**
     * @return void
     */
    public function testSameYearButDifferentAcademicPeriods()
    {
        $student = LegacyStudentFactory::new()->create();

        $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'data_inicio' => now()->subMonths(3),
            'data_fim' => now()->subMonths(2),
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $otherSchoolClass,
            'data_inicio' => now()->addMonths(3),
            'data_fim' => now()->addMonths(2),
        ]);

        $registration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student
        ]);

        $otherRegistration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student
        ]);

        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => $registration,
        ]);

        $this->assertTrue($this->service->isAvailable($otherRegistration->ref_cod_aluno, $otherSchoolClass->cod_turma));
    }

    /**
     * @return void
     */
    public function testWithEnrollmentsSameDaySameTimeSameYearAndOneAeeOtherEscolarizacaoReturnsTrue()
    {
        $schoolClass = LegacySchoolClassFactory::new()->morning()->create(
            [
                'tipo_mediacao_didatico_pedagogico' => 1,
                'tipo_atendimento' => TipoAtendimentoTurma::AEE,
            ]
        );
        $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(
            [
                'tipo_mediacao_didatico_pedagogico' => 1,
                'tipo_atendimento' => TipoAtendimentoTurma::ESCOLARIZACAO,
                'hora_final' => '15:45',
            ]
        );

        $registration = LegacyRegistrationFactory::new()->create(['ano' => $schoolClass->ano]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $otherSchoolClass,
        ]);

        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    /**
     * @return void
     */
    public function testWithInactiveEnrollmentsSameDaySameTimeSameYearReturnsTrue()
    {
        $schoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $otherSchoolClass = LegacySchoolClassFactory::new()->morning()->create(['tipo_mediacao_didatico_pedagogico' => 1]);
        $registration = LegacyRegistrationFactory::new()->create(['ano' => $schoolClass->ano, 'aprovado' => 3]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $otherSchoolClass,
        ]);

        LegacyEnrollmentFactory::new()->inactive()->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }
}
