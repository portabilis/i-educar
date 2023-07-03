<?php

namespace Tests\Api;

use Database\Factories\LegacyKnowledgeAreaFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaAreasConhecimentoTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaAreasConhecimento()
    {
        $knowledgeArea = LegacyKnowledgeAreaFactory::new()->create();
        $knowledgeArea->refresh();

        $data = [
            'oper' => 'get',
            'resource' => 'areas-de-conhecimento',
            'instituicao_id' => $knowledgeArea->instituicao_id,
        ];
        $response = $this->getResource('/module/Api/AreaConhecimento', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'areas' => [
                        0 => [
                            'id' => $knowledgeArea->getKey(),
                            'nome' => $knowledgeArea->nome,
                            'ordenamento_ac' => $knowledgeArea->ordenamento_ac,
                            'agrupar_descritores' => false,
                            'nome_agrupador' => $knowledgeArea->nome,
                            'updated_at' => $knowledgeArea->updated_at->format('Y-m-d H:i:s'),
                            'deleted_at' => null,
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'areas-de-conhecimento',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
