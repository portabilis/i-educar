<?php

namespace Tests\Api;

use Database\Factories\LegacyEnrollmentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use UnitBaseTest;

class DiarioRecuperaBoletimTest extends UnitBaseTest
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaBoletimEstrutura()
    {
        if (class_exists(\BoletimReport::class)) {
            $this->markTestSkipped('Pacote de boletim instalado, o render em teste unitário não esta disponível');
        }

        $enrollment = LegacyEnrollmentFactory::new()->create();
        $data = [
            'oper' => 'get',
            'resource' => 'boletim',
            'matricula_id' => $enrollment->ref_cod_matricula,
            'escola_id' => $enrollment->schoolClass()->first()->schoolId,
        ];

        $response = $this->getResource('/module/Api/Report?', $data);

        $response->assertJsonStructure(
            [
                'oper',
                'resource',
                'msgs',
                'any_error_msg',
                'encoded',
                'encoding',
                'matricula_id',
            ]
        );
    }
}
