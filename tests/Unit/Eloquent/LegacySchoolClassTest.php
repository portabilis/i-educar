<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyEnrollment;
use App\Models\LegacyLevel;
use App\Models\LegacyPeriod;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClassStage;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Support\Collection;
use Tests\EloquentTestCase;

class LegacySchoolClassTest extends EloquentTestCase
{
    private LegacySchoolClass $schoolClass;

    /**
     * @var array
     */
    protected $relations = [
        'course' => LegacyCourse::class,
        'grade' => LegacyLevel::class,
        'school' => LegacySchool::class,
        'period' => LegacyPeriod::class,
        'enrollments' => LegacyEnrollment::class,
        'schoolClassStages' => LegacySchoolClassStage::class,
        'multigrades' => LegacySchoolClassGrade::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClass::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->schoolClass = $this->createNewModel();
    }

    public function testCreateUsingEloquent()
    {
        $this->markTestSkipped();
    }

    public function testUpdateUsingEloquent()
    {
        $this->markTestSkipped();
    }

    public function testDeleteUsingEloquent()
    {
        $this->markTestSkipped();
    }

    /**
     * Serão cadastradas:
     *
     * - 1 enturmação ativa sendo matrícula de depêndencia.
     * - 1 enturmação inativa.
     * - 1 enturmação ativa.
     *
     * O total deve contabilizar:
     *
     * - Apenas enturmações ativas.
     * - Matrículas que não sejam de dependências.
     *
     * @return void
     */
    public function testGetTotalEnrolledMethod()
    {
        /** @var LegacySchoolClass $schoolClass */
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()->create([
            'dependencia' => true,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => $registration,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'ativo' => false,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        $this->assertEquals(1, $schoolClass->getTotalEnrolled());
    }

    /** @test */
    public function getVisibleAttribute()
    {
        $this->assertEquals($this->schoolClass->visivel, $this->schoolClass->getVisibleAttribute());
    }

    /** @test */
    public function getExemptedDisciplineIdAttribute()
    {
        $this->assertEquals($this->schoolClass->ref_cod_disciplina_dispensada, $this->schoolClass->getExemptedDisciplineIdAttribute());
    }

    /** @test */
    public function getActiveEnrollments()
    {
        $this->assertInstanceOf(Collection::class, $this->schoolClass->getActiveEnrollments());
    }

    /** @test */
    public function denyEnrollmentsWhenNoVacancy()
    {
        $this->schoolClass->schoolGrade = null;

        $this->assertEquals(true, $this->schoolClass->denyEnrollmentsWhenNoVacancy());
    }

    /** @test */
    public function getClassTime()
    {
        $this->schoolClass->hora_inicial = null;

        $this->assertEquals(0, $this->schoolClass->getClassTime());
    }
}
