<?php

namespace Tests\Api;

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaServidoresComVinculosDeTurmasEDisciplinasTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaServidoresComVinculosDeTurmasEDisciplinas()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos')
            ->assertJson(
                [
                    'vinculos' => [
                        0 => [
                            'id' => $legacySchoolClassTeacher->getKey(),
                            'servidor_id' => $employee->getKey(),
                            'turma_id' => $schoolClass->getKey(),
                            'turno_id' => $period->getKey(),
                            'permite_lancar_faltas_componente' => 0,
                            'deleted_at' => null,
                            'disciplinas' => [
                                [
                                    'id' => $discipline->getKey(),
                                    'serie_id' => $grade->getKey(),
                                    'tipo_nota' => $legacyDisciplineAcademicYear->tipo_nota,
                                ],
                            ],
                            'disciplinas_serie' => [
                                $grade->getKey() => [$discipline->getKey()],
                            ],
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'servidores-disciplinas-turmas',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            )
            ->assertJsonStructure(
                [
                    'vinculos' => [
                        '*' => [
                            'id',
                            'servidor_id',
                            'turma_id',
                            'turno_id',
                            'permite_lancar_faltas_componente',
                            'updated_at',
                            'deleted_at',
                            'disciplinas',
                            'disciplinas_serie',
                        ],
                    ],
                    'oper',
                    'resource',
                    'msgs',
                    'any_error_msg',
                ]
            );
    }
}
