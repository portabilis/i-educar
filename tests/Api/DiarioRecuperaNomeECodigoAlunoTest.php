<?php

namespace Tests\Api;

use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaNomeECodigoAlunoTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testGetNomeCodigoAluno()
    {
        $student = LegacyStudentFactory::new()->create();
        $data = [
            'oper'=> 'get',
            'resource' => 'aluno-search',
            'query' =>  $student->name
        ];
        $response = $this->getResource('/module/Api/Aluno', $data);

        $response->assertJsonStructure(
            [
                'result',
                'oper',
                'resource',
                'msgs',
                'any_error_msg'
            ]
        );

        $this->assertStringContainsString(
            \Portabilis_String_Utils::toUtf8($student->name, [
                'transform' => true
            ]),
            $response->getContent()
        );
    }
}
