<?php

namespace Tests\Feature\Http\Controllers\Api;

use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\TestCase;

#[Controller]
class SituationControllerTest extends TestCase
{
    #[
        GET('/api/situation', ['Situation'], 'Get all situations'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'Situation')
    ]
    public function testIndex(): void
    {
        $response = $this->get('api/situation');
        $expected = [
            'data' => [
                9 => 'Exceto Transferidos/Abandono',
                0 => 'Todos',
                1 => 'Aprovado',
                2 => 'Reprovado',
                3 => 'Cursando',
                4 => 'Transferido',
                5 => 'Reclassificado',
                6 => 'Abandono',
                8 => 'Aprovado sem exame',
                10 => 'Aprovado após exame',
                12 => 'Aprovado com dependência',
                13 => 'Aprovado pelo conselho',
                14 => 'Reprovado por falta'
            ]
        ];
        $response->assertOk();
        $response->assertJson($expected);
    }
}
