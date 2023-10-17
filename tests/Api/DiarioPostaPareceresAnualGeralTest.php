<?php

namespace Tests\Api;

use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyGeneralDescriptiveOpinion;
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
use Tests\TestCase;

class DiarioPostaPareceresAnualGeralTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testPostaPareceresAnualGeral()
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

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => $discipline,
            'anos_letivos' => $schoolGrade->anos_letivos,
        ]);

        $data = [
            'oper' => 'post',
            'resource' => 'pareceres-anual-geral',
            'pareceres' => [
                $enrollment->ref_cod_turma => [
                    $registration->ref_cod_aluno => [
                        'valor' => $parecer = 'Desenvolve atividades com autonomia e responsabilidade, demonstrando interesse e iniciativa.',
                    ],
                ],
            ],
        ];

        $response = $this->postResource('/module/Api/Diario', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'oper' => 'post',
                    'resource' => 'pareceres-anual-geral',
                    'msgs' => [
                        0 => [
                            'msg' => 'Pareceres postados com sucesso!',
                            'type' => 'success',
                        ],
                    ],
                    'any_error_msg' => false,
                ]
            );

        $generalDescriptiveOpinion = LegacyGeneralDescriptiveOpinion::first();

        $this->assertDatabaseHas($generalDescriptiveOpinion->studentDescriptiveOpinion->getTable(), [
            'matricula_id' => $registration->getKey(),
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
        ]);

        $this->assertDatabaseHas($generalDescriptiveOpinion->getTable(), [
            'parecer_aluno_id' => $generalDescriptiveOpinion->studentDescriptiveOpinion->getKey(),
            'parecer' => $parecer,
            'etapa' => 'An',
        ]);

        $this->assertDatabaseCount($generalDescriptiveOpinion->studentDescriptiveOpinion->getTable(), 1);
        $this->assertDatabaseCount($generalDescriptiveOpinion->getTable(), 1);
        $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);

        //alterando a regra de avaliação da série
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
        ]);
        $evaluationRuleGradeYear->update(['regra_avaliacao_id' => $evaluationRule->getKey()]);
        $response = $this->getResource('/module/Api/Diario', $data);
        $response->assertSuccessful()
            ->assertJson(
                [
                    'error' => [
                        'code' => 0,
                        'message' => "A regra da turma {$enrollment->ref_cod_turma} não permite lançamento de pareceres anual geral.",
                    ],
                    'oper' => 'post',
                    'resource' => 'pareceres-anual-geral',
                    'msgs' => [
                        [
                            'msg' => "A regra da turma {$enrollment->ref_cod_turma} não permite lançamento de pareceres anual geral.",
                            'type' => 'error',
                        ],
                    ],
                    'any_error_msg' => true,
                ]
            );
        $this->assertDatabaseCount($generalDescriptiveOpinion->studentDescriptiveOpinion->getTable(), 1);
        $this->assertDatabaseCount($generalDescriptiveOpinion->getTable(), 1);
    }
}
