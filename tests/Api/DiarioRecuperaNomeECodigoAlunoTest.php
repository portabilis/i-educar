<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

test('get nome codigo aluno', function () {
    $student = LegacyStudentFactory::new()->create([
        'ref_idpes' => fn () => LegacyIndividualFactory::new()->create([
            'idpes' => fn () => LegacyPersonFactory::new()->create([
                'nome' => 'GUSTAVO',
            ]),
        ]),
    ]);
    $data = [
        'oper' => 'get',
        'resource' => 'aluno-search',
        'query' => $student->name,
    ];
    $response = $this->getResource('/module/Api/Aluno', $data);

    $response->assertSuccessful()
        ->assertJsonCount(1, 'result')
        ->assertJson(
            [
                'result' => [
                    $student->getKey() => $student->getKey() . ' - ' . $student->name,
                ],
                'oper' => 'get',
                'resource' => 'aluno-search',
                'msgs' => [],
                'any_error_msg' => false,
            ]
        );
});
