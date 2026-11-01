<?php

use Database\Factories\LegacyCourseFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

test('recupera curso', function () {
    $course = LegacyCourseFactory::new()->create();

    $data = [
        'oper' => 'get',
        'resource' => 'cursos',
        'instituicao_id' => $course->ref_cod_instituicao,
    ];

    $response = $this->getResource('/module/Api/Curso', $data);
    $response->assertJsonStructure(
        [
            'any_error_msg',
            'cursos' => [
                [
                    'id',
                    'nome',
                    'updated_at',
                    'deleted_at',
                ],
            ],
            'msgs',
            'oper',
            'resource',
        ]
    );
});
