<?php

namespace Tests\Feature\Services;

use App\Exceptions\Enrollment\CancellationDateAfterAcademicYearException;
use App\Exceptions\Enrollment\CancellationDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateAfterAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Exceptions\Enrollment\NoVacancyException;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollDateException;
use App\Models\LegacyEnrollment;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyUser;
use App\Services\EnrollmentService;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Throwable;

class EnrollmentServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacySchoolClass
     */
    private $schoolClass;

    /**
     * @var EnrollmentService
     */
    private $service;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $user = factory(LegacyUser::class)->state('unique')->make();

        $user = User::find($user->id);

        $schoolClass = factory(LegacySchoolClass::class)->create();

        factory(LegacySchoolClassStage::class)->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        $this->schoolClass = $schoolClass;
        $this->service = new EnrollmentService($user);
    }

    /**
     * Cancelamento de enturmação com sucesso.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testCancelEnrollment()
    {
        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $result = $this->service->cancelEnrollment($enrollment, now());

        $this->assertTrue($result);
        $this->assertDatabaseHas($enrollment->getTable(), [
            'ref_cod_matricula' => $enrollment->ref_cod_matricula,
            'ref_cod_turma' => $enrollment->ref_cod_turma,
            'ativo' => 0,
        ]);
    }

    /**
     * Erro ao cancelar uma enturmação devido a data de saída ser anterior ao
     * início do ano letivo.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testCancellationDateBeforeAcademicYearException()
    {
        $this->expectException(CancellationDateBeforeAcademicYearException::class);

        $stage = $this->schoolClass->stages()->first();

        $stage->data_inicio = now()->addDay();
        $stage->save();

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $this->service->cancelEnrollment($enrollment, now());
    }

    /**
     * Erro ao cancelar uma enturmação devido a data de saída ser posterior ao
     * fim do ano letivo.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testCancellationDateAfterAcademicYearException()
    {
        $this->expectException(CancellationDateAfterAcademicYearException::class);

        $stage = $this->schoolClass->stages()->first();

        $stage->data_fim = now()->subDay();
        $stage->save();

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $this->service->cancelEnrollment($enrollment, now());
    }

    /**
     * Erro ao cancelar uma enturmação devido a data de saída ser anterior que
     * a data de enturmação.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testPreviousCancellationDateException()
    {
        $this->expectException(PreviousCancellationDateException::class);

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $this->service->cancelEnrollment($enrollment, now()->subDay(1));
    }

    /**
     * Enturmação feita com sucesso.
     *
     * @return void
     */
    public function testEnroll()
    {
        $enrollment = factory(LegacyEnrollment::class)->make([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $result = $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, now()
        );

        $this->assertInstanceOf(LegacyEnrollment::class, $result);
        $this->assertDatabaseHas($enrollment->getTable(), [
            'ref_cod_matricula' => $enrollment->registration_id,
            'ref_cod_turma' => $enrollment->school_class_id,
            'ativo' => 1,
        ]);
    }

    /**
     * Sem vagas na turma.
     *
     * @return void
     */
    public function testNoVacancyException()
    {
        $this->expectException(NoVacancyException::class);

        $enrollment = factory(LegacyEnrollment::class)->make([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $enrollment->schoolClass->max_aluno = 0;

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, now()
        );
    }

    /**
     * Existe uma outra enturmação ativa para a matrícula na turma.
     *
     * @return void
     */
    public function testExistsActiveEnrollmentException()
    {
        $this->expectException(ExistsActiveEnrollmentException::class);

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, now()
        );
    }

    /**
     * Erro ao enturmar uma matrícula devido a data de entrada ser anterior ao
     * início do ano letivo.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testEnrollDateBeforeAcademicYearException()
    {
        $this->expectException(EnrollDateBeforeAcademicYearException::class);

        $enrollment = factory(LegacyEnrollment::class)->make([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $stage = $this->schoolClass->stages()->first();

        $stage->data_inicio = now()->addDay();
        $stage->save();

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, now()
        );
    }

    /**
     * Erro ao enturmar uma matrícula devido a data de entrada ser posterior ao
     * fim do ano letivo.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testEnrollDateAfterAcademicYearException()
    {
        $this->expectException(EnrollDateAfterAcademicYearException::class);

        $enrollment = factory(LegacyEnrollment::class)->make([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $stage = $this->schoolClass->stages()->first();

        $stage->data_fim = now()->subDay();
        $stage->save();

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, now()
        );
    }

    /**
     * A data de enturmação é anterior a data de matrícula.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testPreviousEnrollDateException()
    {
        $this->expectException(PreviousEnrollDateException::class);

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $this->schoolClass,
        ]);

        $this->service->cancelEnrollment($enrollment, now());

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, now()->subDay(1)
        );
    }
}
