<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyEnrollment;
use App\Models\LegacyLevel;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use Illuminate\Support\Collection;
use Tests\EloquentTestCase;

class LegacySchoolClassTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'course' => LegacyCourse::class,
        'grade' => LegacyLevel::class,
        'school' => LegacySchool::class,
        'enrollments' => Collection::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClass::class;
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
        $schoolClass = factory(LegacySchoolClass::class)->create();

        $registration = factory(LegacyRegistration::class)->create([
            'dependencia' => true,
        ]);

        factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => $registration,
        ]);

        factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $schoolClass,
            'ativo' => false,
        ]);

        factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $schoolClass,
        ]);

        $this->assertEquals(1, $schoolClass->getTotalEnrolled());
    }
}
