<?php

namespace Tests\Feature\Services;

use App\Models\LegacyEnrollment;
use App\Models\LegacyUser;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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

        $this->service = new EnrollmentService($user);
    }

    /**
     * @return void
     */
    public function testGetMaxActiveSequence()
    {
        $enrollment = factory(LegacyEnrollment::class)->create();

        $result = $this->service->getMaxActiveSequence($enrollment->ref_cod_matricula, $enrollment->ref_cod_turma);

        $this->assertEquals(1, $result);

        factory(LegacyEnrollment::class)->create([
            'ref_cod_matricula' => $enrollment->ref_cod_matricula,
            'ref_cod_turma' => $enrollment->ref_cod_turma,
            'sequencial' => $enrollment->sequencial + 1,
        ]);

        $result = $this->service->getMaxActiveSequence($enrollment->ref_cod_matricula, $enrollment->ref_cod_turma);

        $this->assertEquals(2, $result);
    }

    /**
     * @return void
     */
    public function testCancelEnrollment()
    {
        $enrollment = factory(LegacyEnrollment::class)->create();

        $result = $this->service->cancelEnrollment(
            $enrollment->ref_cod_matricula, $enrollment->ref_cod_turma, now()
        );

        $this->assertTrue($result);
        $this->assertDatabaseHas($enrollment->getTable(), [
            'ref_cod_matricula' => $enrollment->ref_cod_matricula,
            'ref_cod_turma' => $enrollment->ref_cod_turma,
            'ativo' => 0,
        ]);
    }
}
