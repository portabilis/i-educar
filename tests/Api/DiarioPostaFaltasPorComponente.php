<?php

namespace Tests\Api;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentAbsence;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyKnowledgeAreaFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use RegraAvaliacao_Model_TipoParecerDescritivo;
use RegraAvaliacao_Model_TipoPresenca;
use Tests\TestCase;

class DiarioPostaFaltasPorComponente extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testPostaFaltasPorComponente()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $grade1 = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $grade2 = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade1 = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade1,
            'ref_cod_escola' => $school,
        ]);
        $schoolGrade2 = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade2,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $grade1,
            'ref_cod_curso' => $course,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade1,
            'turma_id' => $schoolClass,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade2,
            'turma_id' => $schoolClass,
        ]);

        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
            'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        ]);

        $areaConhecimento1 = LegacyKnowledgeAreaFactory::new()->create([
            'agrupar_descritores' => true,
        ]);
        $discipline1 = LegacyDisciplineFactory::new()->create([
            'knowledge_area_id' => $areaConhecimento1,
        ]);

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline1,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        $areaConhecimento2 = LegacyKnowledgeAreaFactory::new()->create([
            'agrupar_descritores' => true,
        ]);
        $discipline2 = LegacyDisciplineFactory::new()->create([
            'knowledge_area_id' => $areaConhecimento2,
            'ordenamento' => 2,
            'name' => 'B-Disciplina2',
        ]);

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline2,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        $discipline3 = LegacyDisciplineFactory::new()->create([
            'knowledge_area_id' => $areaConhecimento2,
            'ordenamento' => 1,
            'name' => 'A-Disciplina3',
        ]);

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline3,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        $discipline4 = LegacyDisciplineFactory::new()->create([
            'knowledge_area_id' => $areaConhecimento2,
            'ordenamento' => 3,
            'name' => 'C-Disciplina4',
        ]);

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline4,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline1,
            'ano_escolar_id' => $grade1,
            'anos_letivos' => $schoolGrade1->anos_letivos,
            'hora_falta' => null,
        ]);
        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline2,
            'ano_escolar_id' => $grade2,
            'anos_letivos' => $schoolGrade2->anos_letivos,
            'hora_falta' => null,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline3,
            'ano_escolar_id' => $grade2,
            'anos_letivos' => $schoolGrade2->anos_letivos,
            'hora_falta' => null,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline4,
            'ano_escolar_id' => $grade2,
            'anos_letivos' => $schoolGrade2->anos_letivos,
            'hora_falta' => null,
        ]);

        $evaluationRuleGradeYear1 = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $evaluationRule,
            'serie_id' => $grade1,
        ]);

        $evaluationRuleGradeYear2 = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $evaluationRule,
            'serie_id' => $grade2,
        ]);

        $student1 = LegacyStudentFactory::new()->create();

        $registration1 = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student1,
            'ano' => $evaluationRuleGradeYear1->ano_letivo,
            'ref_ref_cod_serie' => $grade1,
            'ref_ref_cod_escola' => $school,
        ]);

        $student2 = LegacyStudentFactory::new()->create();

        $registration2 = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student2,
            'ano' => $evaluationRuleGradeYear2->ano_letivo,
            'ref_ref_cod_serie' => $grade2,
            'ref_ref_cod_escola' => $school,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration1,
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration2,
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $grade1,
            'ref_cod_disciplina' => $discipline1,
            'etapas_especificas' => 1,
        ]);
        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $grade2,
            'ref_cod_disciplina' => $discipline2,
            'etapas_especificas' => 1,
        ]);
        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $grade2,
            'ref_cod_disciplina' => $discipline3,
            'etapas_especificas' => 1,
        ]);
        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $grade2,
            'ref_cod_disciplina' => $discipline4,
            'etapas_especificas' => 1,
        ]);

        //componente errado que existe na turma, porém não pertence à série da matrícula
        //mensagem de erro ignorada
        $data = $this->getData($schoolClass, $student2, $discipline1);
        $response = $this->getResource('/module/Api/Diario', $data);
        $response->assertSuccessful()
            ->assertJson(
                [
                    'oper' => 'post',
                    'resource' => 'faltas-por-componente',
                    'msgs' => [
                        0 => [
                            'msg' => 'Faltas postadas com sucesso!',
                            'type' => 'success',
                        ],
                    ],
                    'any_error_msg' => false,
                ]
            );
        $this->assertDatabaseCount((new LegacyStudentAbsence)->getTable(), 0);
        $this->assertDatabaseCount((new LegacyDisciplineAbsence())->getTable(), 0);

        //componente errado que não existe na turma
        $discipline5 = LegacyDisciplineFactory::new()->create();
        $data = $this->getData($schoolClass, $student2, $discipline5);
        $this->invalidResponse($data, $discipline5, $schoolClass);

        //agrupadores
        $data = [
            'oper' => 'post',
            'resource' => 'faltas-por-componente',
            'etapa' => 1,
            'faltas' => [
                $schoolClass->getKey() => [
                    $student2->getKey() => [
                        'grouper:2' => [
                            'valor' => 2,
                            'area_do_conhecimento' => $areaConhecimento2->getKey(),
                        ],
                    ],
                ],
            ],
        ];
        //a disciplina 3 é a primeira do agrupamento precisa ter nota
        $studentAbsence = $this->validResponse($data, $registration2, $discipline3);

        //a disciplina 2 não é a primeira e precisa ter nota zerada
        $this->assertDatabaseHas((new LegacyDisciplineAbsence())->getTable(), [
            'componente_curricular_id' => $discipline2->getKey(),
            'falta_aluno_id' => $studentAbsence->getKey(),
            'quantidade' => 0,
        ]);

        //a disciplina 4 precisa ter nota zerada
        $this->assertDatabaseHas((new LegacyDisciplineAbsence())->getTable(), [
            'componente_curricular_id' => $discipline4->getKey(),
            'falta_aluno_id' => $studentAbsence->getKey(),
            'quantidade' => 0,
        ]);

        //a disciplina 1 não pode ter dados
        $this->assertDatabaseMissing((new LegacyDisciplineAbsence())->getTable(), [
            'componente_curricular_id' => $discipline1->getKey(),
            'falta_aluno_id' => $studentAbsence->getKey(),
        ]);

        $this->assertDatabaseCount((new LegacyStudentAbsence)->getTable(), 1);
        $this->assertDatabaseCount((new LegacyDisciplineAbsence())->getTable(), 3);
        //componente da primeira série multisseriada
        $data = $this->getData($schoolClass, $student1, $discipline1);
        $this->validResponse($data, $registration1, $discipline1);
        $this->assertDatabaseCount((new LegacyStudentAbsence)->getTable(), 2);
        $this->assertDatabaseCount((new LegacyDisciplineAbsence())->getTable(), 4);

        //componente de outra série multisseriada
        $data = $this->getData($schoolClass, $student2, $discipline2);
        $this->validResponse($data, $registration2, $discipline2);
        $this->assertDatabaseCount((new LegacyStudentAbsence)->getTable(), 2);
        $this->assertDatabaseCount((new LegacyDisciplineAbsence())->getTable(), 4);
        $this->assertDatabaseEmpty((new LegacyGeneralAbsence())->getTable());
        //alterando a regra de avaliação da série
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
            'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::GERAL,
        ]);
        $evaluationRuleGradeYear2->update(['regra_avaliacao_id' => $evaluationRule->getKey()]);
        $data = $this->getData($schoolClass, $student2, $discipline2);
        $response = $this->getResource('/module/Api/Diario', $data);
        $response->assertSuccessful()
            ->assertJson(
                [
                    'error' => [
                        'code' => 1011,
                        'message' => "A regra da turma {$schoolClass->getKey()} não permite lançamento de faltas por componente.",
                    ],
                    'oper' => 'post',
                    'resource' => 'faltas-por-componente',
                    'msgs' => [
                        [
                            'msg' => "A regra da turma {$schoolClass->getKey()} não permite lançamento de faltas por componente.",
                            'type' => 'error',
                        ],
                    ],
                    'any_error_msg' => true,
                ]
            );
        $this->assertDatabaseCount((new LegacyStudentAbsence)->getTable(), 2);
        $this->assertDatabaseCount((new LegacyDisciplineAbsence())->getTable(), 4);
    }

    private function getData(LegacySchoolClass $schoolClass, LegacyStudent $student, LegacyDiscipline $discipline): array
    {
        return [
            'oper' => 'post',
            'resource' => 'faltas-por-componente',
            'etapa' => 1,
            'faltas' => [
                $schoolClass->getKey() => [
                    $student->getKey() => [
                        $discipline->getKey() => [
                            'valor' => 2,
                        ],
                    ],
                ],
            ],
        ];
    }

    private function validResponse(array $data, LegacyRegistration $registration, LegacyDiscipline $discipline): LegacyStudentAbsence
    {
        $response = $this->getResource('/module/Api/Diario', $data);
        $response->assertSuccessful()
            ->assertJson(
                [
                    'oper' => 'post',
                    'resource' => 'faltas-por-componente',
                    'msgs' => [
                        0 => [
                            'msg' => 'Faltas postadas com sucesso!',
                            'type' => 'success',
                        ],
                    ],
                    'any_error_msg' => false,
                ]
            );

        $studentAbsence = $registration->studentAbsence;
        $this->assertDatabaseHas((new LegacyStudentAbsence)->getTable(), [
            'matricula_id' => $registration->getKey(),
            'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
        ]);

        $this->assertDatabaseHas((new LegacyDisciplineAbsence())->getTable(), [
            'componente_curricular_id' => $discipline->getKey(),
            'falta_aluno_id' => $studentAbsence->getKey(),
            'quantidade' => 2,
            'etapa' => 1,
        ]);

        return $studentAbsence;
    }

    private function invalidResponse(array $data, LegacyDiscipline $discipline, LegacySchoolClass $schoolClass): TestResponse
    {
        $response = $this->getResource('/module/Api/Diario', $data);

        return $response->assertSuccessful()
            ->assertJson(
                [
                    'error' => [
                        'code' => 1010,
                        'message' => "Componente curricular de código {$discipline->getKey()} não existe para a turma {$schoolClass->getKey()}.",
                    ],
                    'oper' => 'post',
                    'resource' => 'faltas-por-componente',
                    'msgs' => [
                        [
                            'msg' => "Componente curricular de código {$discipline->getKey()} não existe para a turma {$schoolClass->getKey()}.",
                            'type' => 'error',
                        ],
                        [
                            'msg' => 'Faltas postadas com sucesso!',
                            'type' => 'success',
                        ],
                    ],
                    'any_error_msg' => true,
                ]
            );
    }
}
