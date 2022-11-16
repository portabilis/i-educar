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
        $enrollment = LegacyEnrollmentFactory::new()->create();
        $data = [
            'oper'=> 'get',
            'resource' => 'boletim',
            'matricula_id' =>  $enrollment->ref_cod_matricula,
            'escola_id' =>  $enrollment->schoolClass()->first()->schoolId
        ];

        $mock = $this->getCleanMock('ReportController');
        $mock->expects($this->once())
            ->method('canGetBoletim')
            ->willReturn(true)
            ->method('generationBoletion')
            ->willReturn('base64Encoded');

        $response = $this->getResource('/module/Api/Report?', $data);

        $response->assertJsonStructure(
            [
                'oper',
                'resource',
                'msgs',
                'any_error_msg',
                'encoded',
                'matricula_id',
            ]
        );
    }
}
