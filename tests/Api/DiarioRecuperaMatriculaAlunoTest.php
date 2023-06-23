<?php

namespace Tests\Api;

use Database\Factories\LegacyEnrollmentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaMatriculaAlunoTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testBuscaMatriculaPorAluno()
    {
        $enrollment = LegacyEnrollmentFactory::new()->create();

        $data = [
            'oper' => 'get',
            'resource' => 'matriculas',
            'aluno_id' => $enrollment->getStudentId(),
        ];

        $response = $this->getResource('/module/Api/Aluno', $data);
        $response->assertJsonStructure(
            [
                'any_error_msg',
                'matriculas' => [
                    [
                        'aluno_id',
                        'aluno_nome',
                        'ano',
                        'curso_id',
                        'curso_nome',
                        'data_entrada',
                        'data_saida',
                        'escola_id',
                        'escola_nome',
                        'id',
                        'instituicao_id',
                        'serie_id',
                        'serie_nome',
                        'situacao',
                        'transferencia_em_aberto',
                        'turma_id',
                        'turma_nome',
                        'ultima_enturmacao',
                        'user_can_access',
                    ],
                ],
                'msgs',
                'oper',
                'resource',
            ]
        );
    }
}
