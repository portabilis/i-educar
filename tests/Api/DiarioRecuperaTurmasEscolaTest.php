<?php

namespace Tests\Api;

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaTurmasEscolaTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaEscolas()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school,
            'ref_cod_curso' => $course,
        ]);

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school,
            'ref_cod_serie' => $level,
        ]);

        $legacyPerid = LegacyPeriodFactory::new()->create();

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
            'turma_turno_id' => $legacyPerid->getKey(),
        ]);

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $level,
            'ref_cod_curso' => $course,
            'aprovado' => 11,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'turmas-por-escola',
            'instituicao_id' => $school->institution->id,
            'escola' => $school->getKey(),
            'ano' => now()->year,
            'turno_id' => $legacyPerid->getKey(),
        ];

        $response = $this->getResource('/module/Api/Turma', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'turmas' => [
                        0 => [
                            'id' => $schoolClass->getKey(),
                            'nome' => $schoolClass->nm_turma,
                            'ano' => $schoolClass->ano,
                            'escola_id' => $school->getKey(),
                            'turno_id' => $schoolClass->turma_turno_id,
                            'curso_id' => null,
                            'series_regras' => [
                                0 => [
                                    'serie_id' => $schoolGrade->grade_id,
                                    'regra_avaliacao_id' => null,
                                ],
                            ],
                            'ref_cod_regente' => null,
                            'updated_at' => $schoolClass->updated_at->format('Y-m-d H:i:s'),
                            'deleted_at' => null,
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'turmas-por-escola',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
