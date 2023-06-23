<?php

namespace Tests\Api;

use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use Database\Factories\LegacyRegistrationDisciplinaryOccurrenceTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioReOcorrenciaDisciplinarTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaOcorrenciaDisciplinar()
    {
        /** @var LegacyRegistrationDisciplinaryOccurrenceType $enrollment */
        $enrollment = LegacyRegistrationDisciplinaryOccurrenceTypeFactory::new()->create(['visivel_pais' => true]);

        $data = [
            'oper' => 'get',
            'resource' => 'ocorrencias_disciplinares',
            'aluno_id' => $enrollment->registration()->first()->ref_cod_aluno,
            'escola' => $enrollment->registration()->first()->ref_ref_cod_escola,
        ];

        $response = $this->getResource('/module/Api/Aluno', $data);

        $response->assertJsonStructure(
            [
                'any_error_msg',
                'msgs',
                'oper',
                'resource',
                'ocorrencias_disciplinares' => [
                    [
                        'data_hora',
                        'descricao',
                        'ocorrencia_disciplinar_id',
                        'tipo',
                        'aluno_id',
                        'escola_id',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ]
        );
    }
}
