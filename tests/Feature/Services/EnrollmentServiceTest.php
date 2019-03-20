<?php

namespace Tests\Feature\Services;

use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Exceptions\Enrollment\NoVacancyException;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollDateException;
use App\Models\LegacyEnrollment;
use App\Models\LegacyUser;
use App\Services\EnrollmentService;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Throwable;

class EnrollmentServiceTest extends TestCase
{
    use DatabaseTransactions;

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
        $enrollment = factory(LegacyEnrollment::class)->create();

        $result = $this->service->cancelEnrollment($enrollment, Carbon::now());

        $this->assertTrue($result);
        $this->assertDatabaseHas($enrollment->getTable(), [
            'ref_cod_matricula' => $enrollment->ref_cod_matricula,
            'ref_cod_turma' => $enrollment->ref_cod_turma,
            'ativo' => 0,
        ]);
    }

    /**
     * Erro ao cancelar uma enturmação devido a data de saída ser menor que a
     * data de enturmação.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testPreviousCancellationDateException()
    {
        $this->expectException(PreviousCancellationDateException::class);

        $enrollment = factory(LegacyEnrollment::class)->create();

        $this->service->cancelEnrollment($enrollment, Carbon::now()->subDay(1));
    }

    /**
     * Enturmação feita com sucesso.
     *
     * @return void
     */
    public function testEnroll()
    {
        $enrollment = factory(LegacyEnrollment::class)->make();

        $result = $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, Carbon::now()
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

        $enrollment = factory(LegacyEnrollment::class)->make();

        $enrollment->schoolClass->max_aluno = 0;

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, Carbon::now()
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

        $enrollment = factory(LegacyEnrollment::class)->create();

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, Carbon::now()
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

        $enrollment = factory(LegacyEnrollment::class)->create();

        $this->service->cancelEnrollment($enrollment, Carbon::now());

        $this->service->enroll(
            $enrollment->registration, $enrollment->schoolClass, Carbon::now()->subDay(1)
        );
    }
}
