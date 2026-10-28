<?php

use Database\Factories\LegacyEnrollmentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;

uses(UnitBaseTest::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

test('recupera boletim estrutura', function () {
    if (class_exists(BoletimReport::class)) {
        $this->markTestSkipped('Pacote de boletim instalado, o render em teste unitário não esta disponível');
    }

    $enrollment = LegacyEnrollmentFactory::new()->create();
    $data = [
        'oper' => 'get',
        'resource' => 'boletim',
        'matricula_id' => $enrollment->ref_cod_matricula,
        'escola_id' => $enrollment->schoolClass()->first()->schoolId,
    ];

    $response = $this->getResource('/module/Api/Report?', $data);

    $response->assertJsonStructure(
        [
            'oper',
            'resource',
            'msgs',
            'any_error_msg',
            'encoded',
            'encoding',
            'matricula_id',
        ]
    );
});
