<?php

namespace Tests\Api;

use App\Models\LegacyEnrollment;
use Database\Factories\LegacyEnrollmentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaTodosAlunosTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testBuscaTodosAlunos()
    {
        /** @var LegacyEnrollment $enrollments */
        $enrollments = LegacyEnrollmentFactory::new()->create();

        $data = [
            'oper' => 'get',
            'resource' => 'todos-alunos',
            'instituicao_id' => $enrollments->schoolClass->ref_cod_instituicao,
            'escola' => $enrollments->schoolClass->ref_ref_cod_escola,
        ];

        $response = $this->getResource('/module/Api/Aluno', $data);

        $response->assertJsonStructure(
            [
                'alunos',
                'any_error_msg',
                'msgs',
                'oper',
                'resource',
            ]
        );
    }
}
