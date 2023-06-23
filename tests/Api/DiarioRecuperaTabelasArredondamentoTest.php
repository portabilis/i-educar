<?php

namespace Tests\Api;

use Database\Factories\LegacyValueRoundingTableFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaTabelasArredondamentoTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaTabelasArredondamento()
    {
        $valueRoundingTable = LegacyValueRoundingTableFactory::new()->create();
        $valueRoundingTable->refresh();

        $data = [
            'oper' => 'get',
            'resource' => 'tabelas-de-arredondamento',
            'instituicao_id' => $valueRoundingTable->roundingTable->instituicao_id,
        ];
        $response = $this->getResource('/module/Api/Regra', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'tabelas' => [
                        0 => [
                            'id' => $valueRoundingTable->roundingTable->getKey(),
                            'nome' => $valueRoundingTable->roundingTable->nome,
                            'tipo_nota' => $valueRoundingTable->roundingTable->tipo_nota,
                            'valores' => [
                                0 => [
                                    'rotulo' => $valueRoundingTable->nome,
                                    'descricao' => $valueRoundingTable->descricao,
                                    'valor_maximo' => number_format($valueRoundingTable->valor_maximo, 3, '.'),
                                    'casa_decimal_exata' => $valueRoundingTable->casa_decimal_exata,
                                    'acao' => $valueRoundingTable->acao,
                                ],
                            ],
                            'updated_at' => $valueRoundingTable->roundingTable->updated_at->format('Y-m-d H:i:s'),
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'tabelas-de-arredondamento',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
