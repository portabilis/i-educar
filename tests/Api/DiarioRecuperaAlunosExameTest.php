<?php

namespace Tests\Api;

use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacyScoreExamFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaAlunosExameTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaAlunosExame()
    {
        $scoreExam = LegacyScoreExamFactory::new()->create();

        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $scoreExam->registration->getKey(),
        ]);

        $disciplineScoreAverage = LegacyDisciplineScoreAverageFactory::new()->create([
            'nota_aluno_id' => LegacyRegistrationScoreFactory::new()->create([
                'matricula_id' => $scoreExam->registration->getKey(),
            ]),
            'componente_curricular_id' => $scoreExam->ref_cod_componente_curricular,
            'situacao' => 7,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'alunos-exame-turma',
            'instituicao_id' => $scoreExam->registration->school->institution->getKey(),
            'turma_id' => $scoreExam->registration->lastEnrollment->ref_cod_turma,
            'disciplina_id' => $scoreExam->ref_cod_componente_curricular,
        ];

        $response = $this->getResource('/module/Api/Turma', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'alunos' => [
                        0 => [
                            'id' => $scoreExam->registration->ref_cod_aluno,
                            'nota_exame' => number_format($scoreExam->nota_exame, 3, '.'),
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'alunos-exame-turma',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
