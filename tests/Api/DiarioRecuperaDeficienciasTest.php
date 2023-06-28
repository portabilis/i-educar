<?php

namespace Tests\Api;

use Database\Factories\LegacyDeficiencyFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaDeficienciasTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaDeficiencias()
    {
        LegacyDeficiencyFactory::new()->create();

        $data = [
            'oper' => 'get',
            'resource' => 'deficiencias',
        ];

        $response = $this->getResource('/module/Api/Deficiencia', $data);
        $response->assertJsonStructure(
            [
                'any_error_msg',
                'deficiencias' => [
                    [
                        'id',
                        'nome',
                        'desconsidera_regra_diferenciada',
                        'updated_at',
                        'deleted_at',
                        'alunos',
                    ],
                ],
                'msgs',
                'oper',
                'resource',
            ]
        );
    }
}
