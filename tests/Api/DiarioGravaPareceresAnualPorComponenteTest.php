<?php

namespace Tests\Api;

use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyGeneralDescriptiveOpinion;
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
use RegraAvaliacao_Model_TipoParecerDescritivo;
use Tests\TestCase;

class DiarioGravaPareceresAnualPorComponenteTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testDiarioGranaPareceresAnualPorComponente()
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
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
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
            'resource' => 'pareceres-anual-por-componente',
            'etapa' => 1,
            'pareceres' => [
                $schoolClass->getKey() => [
                    $student->getKey() => [
                        $discipline->getKey() => [
                            'valor' => $parecer = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->getResource('/module/Api/Diario', $data);
        $response->assertSuccessful();
        $response->assertJson(
            [
                'oper' => 'post',
                'resource' => 'pareceres-anual-por-componente',
                'msgs' => [
                    [
                        'msg' => 'Pareceres postados com sucesso!',
                        'type' => 'success',
                    ],
                ],
                'any_error_msg' => false,
            ]
        );

        $disciplineDescriptiveOpinion = LegacyDisciplineDescriptiveOpinion::first();

        $this->assertDatabaseHas($disciplineDescriptiveOpinion->studentDescriptiveOpinion->getTable(), [
            'matricula_id' => $registration->getKey(),
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
        ]);
        $this->assertDatabaseHas($disciplineDescriptiveOpinion->getTable(), [
            'parecer_aluno_id' => $disciplineDescriptiveOpinion->studentDescriptiveOpinion->getKey(),
            'componente_curricular_id' => $discipline->getKey(),
            'parecer' => $parecer,
            'etapa' => 'An',
        ]);
        $this->assertDatabaseCount($disciplineDescriptiveOpinion->studentDescriptiveOpinion->getTable(), 1);
        $this->assertDatabaseCount($disciplineDescriptiveOpinion->getTable(), 1);
        $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);

        //alterando a regra de avaliação da série
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
        ]);
        $evaluationRuleGradeYear->update(['regra_avaliacao_id' => $evaluationRule->getKey()]);
        $response = $this->getResource('/module/Api/Diario', $data);
        $response->assertSuccessful()
            ->assertJson(
                [
                    'error' => [
                        'code' => 0,
                        'message' => "A regra da turma {$schoolClass->getKey()} não permite lançamento de pareceres anual por componente.",
                    ],
                    'oper' => 'post',
                    'resource' => 'pareceres-anual-por-componente',
                    'msgs' => [
                        [
                            'msg' => "A regra da turma {$schoolClass->getKey()} não permite lançamento de pareceres anual por componente.",
                            'type' => 'error',
                        ],
                    ],
                    'any_error_msg' => true,
                ]
            );
        $this->assertDatabaseCount($disciplineDescriptiveOpinion->studentDescriptiveOpinion->getTable(), 1);
        $this->assertDatabaseCount($disciplineDescriptiveOpinion->getTable(), 1);
    }
}
