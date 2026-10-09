<?php

use Database\Factories\LegacyRemedialRuleFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

test('recupera regras recuperacao', function () {
    $remedialRule = LegacyRemedialRuleFactory::new()->create();

    $data = [
        'oper' => 'get',
        'resource' => 'regras-recuperacao',
        'instituicao_id' => $remedialRule->evaluationRule->instituicao_id,
    ];
    $response = $this->getResource('/module/Api/Regra', $data);

    $response->assertSuccessful()
        ->assertJson(
            [
                'regras-recuperacao' => [
                    0 => [
                        'id' => $remedialRule->getKey(),
                        'regra_avaliacao_id' => $remedialRule->evaluationRule->getKey(),
                        'descricao' => $remedialRule->descricao,
                        'etapas_recuperadas' => [
                            $remedialRule->etapas_recuperadas,
                        ],
                        'media' => $remedialRule->media,
                        'nota_maxima' => $remedialRule->nota_maxima,
                        'updated_at' => $remedialRule->updated_at->format('Y-m-d H:i:s'),
                        'deleted_at' => null,
                    ],
                ],
                'oper' => 'get',
                'resource' => 'regras-recuperacao',
                'msgs' => [],
                'any_error_msg' => false,
            ]
        );
});
