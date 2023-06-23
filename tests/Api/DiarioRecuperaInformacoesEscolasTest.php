<?php

namespace Tests\Api;

use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaInformacoesEscolasTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaInformacoesEscolas()
    {
        $school = LegacySchoolFactory::new()->create();

        $data = [
            'oper' => 'get',
            'resource' => 'info-escolas',
        ];
        $response = $this->getResource('/module/Api/Escola', $data);

        $response->assertSuccessful()
            ->assertJsonStructure(
                [
                    'escolas' => [
                        0 => [
                            'cod_escola',
                            'nome',
                            'cep',
                            'numero',
                            'complemento',
                            'logradouro',
                            'bairro',
                            'municipio',
                            'uf',
                            'pais',
                            'email',
                            'ddd',
                            'fone',
                            'nome_responsavel',
                            'inep',
                            'ativo',
                        ],
                    ],
                    'oper',
                    'resource',
                    'msgs',
                    'any_error_msg',
                ]
            )
            ->assertJson(
                [
                    'escolas' => [
                        0 => [
                            'cod_escola' => $school->getKey(),
                            'nome' => $school->name,
                            'cep' => $school->person->address->cep,
                            'numero' => $school->person->address->numero,
                            'complemento' => $school->person->address->complemento,
                            'logradouro' => $school->person->address->cep,
                            'bairro' => $school->person->place->address,
                            'email' => $school->person->email,
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'info-escolas',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
