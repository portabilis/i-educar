<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('index', #[
    GET('/api/situation', ['Situation'], 'Get all situations'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Situation')
] function () {
    $response = $this->get('api/situation');
    $expected = [
        'data' => [
            1 => 'Aprovado',
            2 => 'Reprovado',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Deixou de Frequentar',
            9 => 'Exceto Transferidos/Deixou de Frequentar',
            10 => 'Todas',
            12 => 'Aprovado com dependência',
            13 => 'Aprovado pelo conselho',
            14 => 'Reprovado por faltas',
            15 => 'Falecido',
        ],
    ];
    $response->assertOk();
    $response->assertJson($expected);
});
