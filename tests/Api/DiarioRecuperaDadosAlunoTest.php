<?php

namespace Tests\Api;

use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaDadosAlunoTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaDadosAluno()
    {
        $student = LegacyStudentFactory::new()->create();
        $data = [
            'oper' => 'get',
            'resource' => 'aluno',
            'id' => $student->cod_aluno,
        ];
        $response = $this->getResource('/module/Api/Aluno', $data);

        $response->assertJsonStructure(
            [
                'alfabetizado',
                'aluno_estado_id',
                'aluno_inep_id',
                'any_error_msg',
                'ativo',
                'beneficio_id',
                'beneficios',
                'codigo_sistema',
                'destroyed_at',
                'destroyed_by',
                'id',
                'justificativa_falta_documentacao',
                'recursos_prova_inep',
                'msgs',
                'nome',
                'oper',
                'pessoa_id',
                'projetos',
                'religiao_id',
                'resource',
                'sus',
                'tipo_responsavel',
                'tipo_transporte',
                'url_laudo_medico',
            ]
        );
    }
}
