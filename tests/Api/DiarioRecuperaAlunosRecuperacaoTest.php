<?php

namespace Tests\Api;

use Database\Factories\LegacyDisciplineDependenceFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaAlunosRecuperacaoTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaDadosAluno()
    {
        $school = LegacySchoolFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'dependencia' => 't',
            'aprovado' => 12,
        ]);

        $dependence = LegacyDisciplineDependenceFactory::new()->create([
            'ref_cod_matricula' => $registration,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'matriculas-dependencia',
            'ano' => now()->year,
            'escola' => $school->getKey(),
        ];

        $response = $this->getResource('/module/Api/Matricula', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'matriculas' => [
                        0 => [
                            'matricula_id' => $registration->getKey(),
                            'disciplina_id' => $dependence->ref_cod_disciplina,
                            'updated_at' => $dependence->updated_at->format('Y-m-d H:i:s'),
                            'deleted_at' => null,
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'matriculas-dependencia',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
