<?php

namespace Tests\Api;

use Database\Factories\LegacyDisciplineFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaDisciplinasTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaDisciplinas()
    {
        $discipline = LegacyDisciplineFactory::new()->create();
        $discipline->refresh();

        $data = [
            'oper' => 'get',
            'resource' => 'componentes-curriculares',
            'instituicao_id' => $discipline->instituicao_id,
        ];
        $response = $this->getResource('/module/Api/ComponenteCurricular', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'disciplinas' => [
                        0 => [
                            'id' => $discipline->getKey(),
                            'nome' => $discipline->nome,
                            'area_conhecimento_id' => $discipline->knowledgeArea->getKey(),
                            'nome_area' => $discipline->knowledgeArea->nome,
                            'ordenamento' => $discipline->ordenamento,
                            'updated_at' => $discipline->updated_at->format('Y-m-d H:i:s'),
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'componentes-curriculares',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
