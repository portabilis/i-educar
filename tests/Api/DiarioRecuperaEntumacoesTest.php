<?php

namespace Tests\Api;

use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaEntumacoesTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaEnturmacoes()
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
            'resource' => 'movimentacao-enturmacao',
            'ano' => now()->year,
            'escola' => $school->getKey(),
        ];

        $response = $this->getResource('/module/Api/Matricula', $data);

        $response->assertSuccessful()
            ->assertJson(
                [
                    'enturmacoes' => [
                        0 => [
                            'id' => $enrollment->getKey(),
                            'turma_id' => $enrollment->ref_cod_turma,
                            'matricula_id' => $enrollment->ref_cod_matricula,
                            'sequencial' => 1,
                            'remanejado_mesma_turma' => false,
                            'sequencial_fechamento' => 0,
                            'data_entrada' => $enrollment->data_enturmacao->format('Y-m-d'),
                            'data_saida' => $enrollment->data_saida ? $enrollment->data_saida->format('Y-m-d') : '',
                            'apresentar_fora_da_data' => false,
                            'turno_id' => $enrollment->turno_id,
                            'serie_id' => $registration->ref_ref_cod_serie,
                            'updated_at' => $enrollment->updated_at->format('Y-m-d H:i:s'),
                            'deleted_at' => $enrollment->deleted_at?->format('Y-m-d H:i:s'),
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'movimentacao-enturmacao',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
