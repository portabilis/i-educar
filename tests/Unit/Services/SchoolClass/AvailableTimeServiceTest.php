<?php

namespace Tests\Unit\Services\SchoolClass;

use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Registration;
use App\Services\SchoolClass\AvailableTimeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AvailableTimeServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var AvailableTimeService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(AvailableTimeService::class);
        $this->disableForeignKeys();
    }

    public function tearDown()
    {
        $this->enableForeignKeys();
        parent::tearDown();
    }

    public function testWithoutOthersEnrollmentsReturnsTrue()
    {
        $schoolClass = factory(SchoolClass::class)->create([ 'tipo_mediacao_didatico_pedagogico' => 1 ]);
        $registration = factory(Registration::class)->create();

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    public function testWithEnrollmentsSameDayDifferentTimeReturnsTrue()
    {
        $schoolClass = factory(SchoolClass::class, 'morning')->create([ 'tipo_mediacao_didatico_pedagogico' => 1 ]);
        $otherSchoolClass = factory(SchoolClass::class, 'afternoon')->create([ 'tipo_mediacao_didatico_pedagogico' => 1 ]);
        $registration = factory(Registration::class)->create();
        factory(Enrollment::class)->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);


        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    public function testWithEnrollmentsSameDaySameTimeReturnsFalse()
    {
        $schoolClass = factory(SchoolClass::class, 'morning')->create([ 'tipo_mediacao_didatico_pedagogico' => 1 ]);
        $otherSchoolClass = factory(SchoolClass::class, 'morning')->create([ 'tipo_mediacao_didatico_pedagogico' => 1 ]);
        $registration = factory(Registration::class)->create();
        factory(Enrollment::class)->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        $this->assertFalse($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    public function testWithEnrollmentsDifferentDaySameTimeReturnsFalse()
    {
        $schoolClass = factory(SchoolClass::class, 'morning')->create([
            'tipo_mediacao_didatico_pedagogico' => 1 ,
            'dias_semana' => '{1, 7}',
        ]);
        $otherSchoolClass = factory(SchoolClass::class, 'morning')->create([ 'tipo_mediacao_didatico_pedagogico' => 1 ]);
        $registration = factory(Registration::class)->create();
        factory(Enrollment::class)->create([
            'ref_cod_turma' => $otherSchoolClass->cod_turma,
            'ref_cod_matricula' => $registration->cod_matricula,
        ]);

        $this->assertTrue($this->service->isAvailable($registration->ref_cod_aluno, $schoolClass->cod_turma));
    }

    public function testShouldLaunchExceptionWhenPassInvalidSchoolClassId()
    {
        $this->expectException(ModelNotFoundException::class);
        $registration = factory(Registration::class)->create();

        $this->service->isAvailable($registration->ref_cod_aluno, -1);
    }
}
