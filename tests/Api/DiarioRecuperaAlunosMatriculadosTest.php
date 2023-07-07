<?php

namespace Tests\Api;

use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaAlunosMatriculadosTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaAlunosMatriculados()
    {
        $school = LegacySchoolFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'dependencia' => 't',
            'aprovado' => 12,
        ]);

        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'alunos-matriculados',
            'instituicao_id' => $school->institution->id,
            'escola_id' => $school->getKey(),
            'ano' => $enrollment->data_enturmacao->year,
            'data' => $enrollment->data_enturmacao->format('Y-m-d'),
        ];
        $response = $this->getResource('/module/Api/Aluno', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'alunos' => [
                        0 => [
                            'aluno_id' => $registration->ref_cod_aluno,
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'alunos-matriculados',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
