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
     *
     * @throws \Throwable
     */
    public function testCancelEnrollment()
    {
        $enrollment = factory(LegacyEnrollment::class)->create();

        $result = $this->service->cancelEnrollment($enrollment->id, now());

        $this->assertTrue($result);
        $this->assertDatabaseHas($enrollment->getTable(), [
            'ref_cod_matricula' => $enrollment->ref_cod_matricula,
            'ref_cod_turma' => $enrollment->ref_cod_turma,
            'ativo' => 0,
        ]);
    }

    /**
     * @return void
     */
    public function testGetBySchoolClass()
    {
        $enrollment = factory(LegacyEnrollment::class)->create();

        factory(LegacyEnrollment::class, 4)->create([
            'ref_cod_turma' => $enrollment->schoolClass->id
        ]);

        $enrollments = $this->service->getBySchoolClass(
            $enrollment->schoolClass->id, $enrollment->schoolClass->year
        );

        $this->assertEquals(5, $enrollments->count());
    }
}
