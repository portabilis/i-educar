<?php

namespace Tests\Api;

use Database\Factories\LegacyGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaBloqueioFaixaEtariaSerieTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaBloqueioFaixaEtariaSerie()
    {
        $grade = LegacyGradeFactory::new()->create();

        $data = [
            'oper' => 'get',
            'resource' => 'bloqueio-faixa-etaria',
            'instituicao_id' => $grade->course->ref_cod_instituicao,
            'serie_id' => $grade->getKey(),
            'data_nascimento' => now()->subYears($grade->idade_inicial)->format('Y-m-d'),
        ];

        $response = $this->getResource('/module/Api/Serie', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'bloqueado' => false,
                    'mensagem_bloqueio' => '',
                    'oper' => 'get',
                    'resource' => 'bloqueio-faixa-etaria',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
