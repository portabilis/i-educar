<?php

namespace Tests\Api;

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyGeneralScore;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use RegraAvaliacao_Model_TipoRecuperacaoParalela;
use Tests\TestCase;

class DiarioGravaRecuperacoesTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testGravaNota()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()
            ->standardAcademicYear()
            ->create(['ref_cod_instituicao' => $school->ref_cod_instituicao]);

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $year = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        $stage = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'num_etapas' => 4,
        ]);

        LegacyAcademicYearStageFactory::new()->create([
            'ref_ano' => $year->ano,
            'ref_ref_cod_escola' => $school,
            'ref_cod_modulo' => $stage,
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
            'tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA,
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

        $student = LegacyStudentFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student,
            'ano' => $evaluationRuleGradeYear->ano_letivo,
            'ref_ref_cod_serie' => $level,
            'ref_ref_cod_escola' => $school,
            'ref_cod_curso' => $course,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => $discipline,
            'anos_letivos' => $schoolGrade->anos_letivos,
        ]);

        $data = [
            'oper' => 'post',
            'resource' => 'notas',
            'etapa' => 1,
            'notas' => [
                $schoolClass->getKey() => [
                    $student->getKey() => [
                        $discipline->getKey() => [
                            'nota' => 6,
                            'recuperacao' => 2,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->getResource('/module/Api/Diario', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'oper' => 'post',
                    'resource' => 'notas',
                    'msgs' => [
                        [
                            'msg' => 'Notas postadas com sucesso!',
                            'type' => 'success',
                        ],
                    ],
                    'any_error_msg' => false,
                ]
            );
        $disciplineScore = LegacyDisciplineScore::first();

        $this->assertDatabaseHas($disciplineScore->registrationScore->getTable(), [
            'matricula_id' => $registration->getKey(),
        ]);

        $this->assertDatabaseHas($disciplineScore->getTable(), [
            'nota_aluno_id' => $disciplineScore->registrationScore->getKey(),
            'componente_curricular_id' => $discipline->getKey(),
            'nota' => 6,
            'nota_arredondada' => 6.0,
            'etapa' => 1,
            'nota_recuperacao' => 2,
            'nota_original' => 6,
            'nota_recuperacao_especifica' => null,
        ]);

        $data = [
            'oper' => 'post',
            'resource' => 'recuperacoes',
            'etapa' => 1,
            'notas' => [
                $schoolClass->getKey() => [
                    $student->getKey() => [
                        $discipline->getKey() => [
                            'nota' => 6,
                            'recuperacao' => 7,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->getResource('/module/Api/Diario', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'oper' => 'post',
                    'resource' => 'recuperacoes',
                    'msgs' => [
                        [
                            'msg' => 'Recuperacoes postadas com sucesso!',
                            'type' => 'success',
                        ],
                    ],
                    'any_error_msg' => false,
                ]
            );
        $disciplineScore = LegacyDisciplineScore::first();

        $this->assertDatabaseHas($disciplineScore->registrationScore->getTable(), [
            'matricula_id' => $registration->getKey(),
        ]);

        $this->assertDatabaseHas($disciplineScore->getTable(), [
            'nota_aluno_id' => $disciplineScore->registrationScore->getKey(),
            'componente_curricular_id' => $discipline->getKey(),
            'nota' => 7,
            'nota_arredondada' => 7.0,
            'etapa' => 1,
            'nota_recuperacao' => 7,
            'nota_original' => 6,
            'nota_recuperacao_especifica' => null,
        ]);

        $this->assertDatabaseCount($disciplineScore->registrationScore->getTable(), 1);
        $this->assertDatabaseCount($disciplineScore->getTable(), 1);
        $this->assertDatabaseCount(LegacyGeneralScore::class, 0);
    }
}
