<?php

namespace Tests\Api;

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyGeneralAbsence;
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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use RegraAvaliacao_Model_TipoParecerDescritivo;
use RegraAvaliacao_Model_TipoPresenca;
use Tests\TestCase;

class DiarioPostaFaltasGeralTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testPostaFaltasGeral()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
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
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::GERAL,
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
            'hora_falta' => null,
        ]);

        $evaluationRuleGradeYear = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $evaluationRule->getKey(),
            'serie_id' => $level,
        ]);

        $registration = LegacyRegistrationFactory::new()->create([
            'ano' => $evaluationRuleGradeYear->ano_letivo,
            'ref_ref_cod_serie' => $level,
            'ref_ref_cod_escola' => $school,
        ]);

        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
        ]);

        $schoolGradeDiscipline = LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => $discipline,
            'anos_letivos' => $schoolGrade->anos_letivos,
        ]);

        $data = [
            'oper' => 'post',
            'resource' => 'faltas-geral',
            'etapa' => 1,
            'faltas' => [
                $enrollment->ref_cod_turma => [
                    $registration->ref_cod_aluno => [
                        'valor' => 2,
                    ],
                ],
            ],
        ];

        $response = $this->postResource('/module/Api/Diario', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'oper' => 'post',
                    'resource' => 'faltas-geral',
                    'msgs' => [
                        0 => [
                            'msg' => 'Faltas postadas com sucesso!',
                            'type' => 'success',
                        ],
                    ],
                    'any_error_msg' => false,
                ]
            );

        $absence = LegacyGeneralAbsence::first();

        $this->assertDatabaseHas($absence->studentAbsence->getTable(), [
            'matricula_id' => $registration->getKey(),
            'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::GERAL,
        ])->assertDatabaseHas($absence->getTable(), [
            'falta_aluno_id' => $absence->studentAbsence->getKey(),
            'quantidade' => 2,
            'etapa' => 1,
        ])->assertDatabaseCount($absence->studentAbsence->getTable(), 1)
            ->assertDatabaseCount($absence->getTable(), 1);
        $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);

        //alterando a regra de avaliação da série
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
            'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        ]);
        $evaluationRuleGradeYear->update(['regra_avaliacao_id' => $evaluationRule->getKey()]);
        $data = [
            'oper' => 'post',
            'resource' => 'faltas-geral',
            'etapa' => 1,
            'faltas' => [
                $enrollment->ref_cod_turma => [
                    $registration->ref_cod_aluno => [
                        'valor' => 2,
                    ],
                ],
            ],
        ];
        $response = $this->getResource('/module/Api/Diario', $data);
        $response->assertSuccessful()
            ->assertJson(
                [
                    'error' => [
                        'code' => 1008,
                        'message' => "A regra da turma {$enrollment->ref_cod_turma} não permite lançamento de faltas geral.",
                    ],
                    'oper' => 'post',
                    'resource' => 'faltas-geral',
                    'msgs' => [
                        [
                            'msg' => "A regra da turma {$enrollment->ref_cod_turma} não permite lançamento de faltas geral.",
                            'type' => 'error',
                        ],
                    ],
                    'any_error_msg' => true,
                ]
            );
        $this->assertDatabaseCount($absence->studentAbsence->getTable(), 1);
        $this->assertDatabaseCount($absence->getTable(), 1);
    }
}
