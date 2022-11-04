<?php

namespace Tests\Api;

use Database\Factories\LegacyGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaSerieTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaCurso()
    {
        $grade = LegacyGradeFactory::new()->create();

        $data = [
            'oper'=> 'get',
            'resource' => 'series',
            'instituicao_id' =>  $grade->course->ref_cod_instituicao
        ];

        $response = $this->getResource('/module/Api/Serie', $data);
        $response->assertJsonStructure(
            [
                'any_error_msg',
                'series' => [
                    [
                        'id',
                        'nome',
                        'idade_padrao',
                        'curso_id',
                        'updated_at',
                        'deleted_at',
                    ]
                ],
                'msgs',
                'oper',
                'resource'
            ]
        );
    }
}
