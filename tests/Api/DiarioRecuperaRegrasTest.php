<?php

namespace Tests\Api;

use Database\Factories\LegacyEvaluationRuleFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaRegrasTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaRegras()
    {
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create();
        $evaluationRule->refresh();

        $data = [
            'oper' => 'get',
            'resource' => 'regras',
            'instituicao_id' => $evaluationRule->instituicao_id,
        ];

        $response = $this->getResource('/module/Api/Regra', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'regras' => [
                        0 => [
                            'id' => $evaluationRule->getKey(),
                            'tabela_arredondamento_id' => $evaluationRule->tabela_arredondamento_id,
                            'tabela_arredondamento_id_conceitual' => $evaluationRule->tabela_arredondamento_id_conceitual,
                            'tipo_nota' => $evaluationRule->tipo_nota,
                            'tipo_presenca' => $evaluationRule->tipo_presenca,
                            'parecer_descritivo' => $evaluationRule->parecer_descritivo,
                            'tipo_recuperacao' => $evaluationRule->tipo_recuperacao_paralela,
                            'media_recuperacao_paralela' => $evaluationRule->media_recuperacao_paralela,
                            'nota_maxima_geral' => $evaluationRule->nota_maxima_geral,
                            'nota_maxima_exame' => $evaluationRule->nota_maxima_exame_final,
                            'updated_at' => $evaluationRule->updated_at->format('Y-m-d H:i:s'),
                            'regra_diferenciada_id' => $evaluationRule->regra_diferenciada_id,
                            'tipo_calculo_recuperacao_paralela' => $evaluationRule->tipo_calculo_recuperacao_paralela,
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'regras',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
