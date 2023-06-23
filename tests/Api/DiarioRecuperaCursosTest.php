<?php

namespace Tests\Api;

use Database\Factories\LegacyCourseFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaCursosTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaCurso()
    {
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
    }
}
