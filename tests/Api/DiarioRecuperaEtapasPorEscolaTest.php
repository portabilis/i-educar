<?php

namespace Tests\Api;

use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyStageTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaEtapasPorEscolaTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaEtapasPorEscola()
    {
        $school = LegacySchoolFactory::new()->create();

        $year = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        $stage = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
        ]);

        $academicYearStage = LegacyAcademicYearStageFactory::new()->create([
            'ref_ano' => $year->ano,
            'ref_ref_cod_escola' => $school,
            'ref_cod_modulo' => $stage,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'etapas-por-escola',
            'ano' => now()->year,
            'instituicao_id' => $school->ref_cod_instituicao,
        ];

        $response = $this->getResource('/module/Api/Escola', $data);
        $response->assertSuccessful()
            ->assertJson(
                [
                    'escolas' => [
                        0 => [
                            'escola_id' => $school->getKey(),
                            'ano' => $year->ano,
                            'descricao' => $stage->nm_tipo,
                            'ano_em_aberto' => $year->andamento,
                            'etapas' => [
                                0 => [
                                    'etapa' => 1,
                                    'data_inicio' => $academicYearStage->data_inicio->format('Y-m-d'),
                                    'data_fim' => $academicYearStage->data_fim->format('Y-m-d'),
                                    'dias_letivos' => $academicYearStage->dias_letivos,
                                ],
                            ],
                            'etapas_de_turmas' => [],
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'etapas-por-escola',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            );
    }
}
