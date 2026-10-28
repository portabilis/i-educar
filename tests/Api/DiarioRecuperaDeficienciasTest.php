<?php

use Database\Factories\LegacyDeficiencyFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

test('recupera deficiencias', function () {
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
});
