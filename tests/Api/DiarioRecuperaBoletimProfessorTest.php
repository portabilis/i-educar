<?php

namespace Tests\Api;

use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaBoletimProfessorTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaBoletimProfessor()
    {
        $class = LegacySchoolClassFactory::new()->create();

        $configuration = LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => $class->school->ref_cod_instituicao,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'turma_id' => $class
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'boletim-professor',
            'ano' => $class->ano,
            'instituicao_id' => $class->school->ref_cod_instituicao,
            'escola_id' => $class->school->getKey(),
            'curso_id' => $class->course->getKey(),
            'serie_id' => $class->grade->getKey(),
            'turma_id' => $class->getKey(),
            'componente_curricular_id' => $discipline->getKey(),
        ];

        $response = $this->getResource('/module/Api/Report', $data);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'encoding',
                'encoded',
                'oper',
                'resource',
                'msgs',
                'any_error_msg',
            ])
            ->assertJson(
                [
                    'encoding' => 'base64',
                    'oper' => 'get',
                    'resource' => 'boletim-professor',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            );
    }
}
