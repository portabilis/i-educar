<?php

namespace Tests\Api;

use App\Models\LegacyDisciplineAbsence;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioPostaFaltasPorComponente extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testPostaFaltasPorComponente()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200'
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $level,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => 3,
            'tipo_presenca' => 2,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
        ]);

        $evaluationRuleGradeYear = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $evaluationRule->getKey(),
            'serie_id' => $level,
        ]);

        $student = LegacyStudentFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student,
            'ano' => $evaluationRuleGradeYear->ano_letivo,
            'ref_ref_cod_serie' => $level,
            'ref_ref_cod_escola' => $school,
        ]);

        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'ref_cod_turma' => $schoolClass,
        ]);

        $schoolGradeDiscipline = LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => $discipline,
            'etapas_especificas' => 1,
        ]);

        $data = [
            'oper' => 'post',
            'resource' => 'faltas-por-componente',
            'etapa' => 1,
            'faltas' => [
                $enrollment->ref_cod_turma => [
                    $registration->ref_cod_aluno => [
                        $discipline->getKey() => [
                            'valor' => 2,
                        ],
                    ]
                ]
            ]
        ];

        $response = $this->getResource('/module/Api/Diario', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'oper' => 'post',
                    'resource' => 'faltas-por-componente',
                    'msgs' => [
                        0 => [
                            'msg' => 'Faltas postadas com sucesso!',
                            'type' => 'success'
                        ]
                    ],
                    'any_error_msg' => false
                ]
            );

        $absence = LegacyDisciplineAbsence::first();

        $this->assertDatabaseHas($absence->studentAbsence->getTable(), [
            'matricula_id' => $registration->getKey(),
            'tipo_falta' => 2,
        ])->assertDatabaseHas($absence->getTable(), [
            'falta_aluno_id' => $absence->studentAbsence->getKey(),
            'quantidade' => 2,
            'etapa' => 1,
        ])->assertDatabaseCount($absence->studentAbsence->getTable(), 1)
            ->assertDatabaseCount($absence->getTable(), 1);
    }
}
