<?php

use Database\Factories\LegacyLevelFactory;
use Database\Factories\LegacyRegistrationFactory;
use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/TestCommon.php';

/**
 * Class AvaliacaoServiceSituacaoTest
 */
class AvaliacaoServiceSituacaoTest extends Avaliacao_Service_TestCommon
{
    public function testSituacaoAluno()
    {
        $nota = new \stdClass();
        $falta = new \stdClass();

        /** @var MockObject|Avaliacao_Service_Boletim $service */
        $service = $this
            ->setExcludedMethods(
                [
                    'getSituacaoAluno',
                    'getSituacaoNotaFalta'
                ]
            )
            ->getCleanMock('Avaliacao_Service_Boletim');

        $regra = $this->getCleanMock('RegraAvaliacao_Model_Regra');

        $regra
            ->method('get')
            ->willReturn(0);

        $service
            ->method('getRegra')
            ->willReturn($regra);

        $registration = LegacyRegistrationFactory::new()
            ->create(
                [
                    'ref_ref_cod_serie' => LegacyLevelFactory::new()->create(),
                    'dependencia' => true,
                ]
            );

        $service
            ->method('getOption')
            ->willReturn($registration->toArray());

        $notaSituacoes = [
            1 => App_Model_MatriculaSituacao::APROVADO,
            2 => App_Model_MatriculaSituacao::APROVADO_APOS_EXAME,
            3 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
            4 => App_Model_MatriculaSituacao::EM_EXAME,
            5 => App_Model_MatriculaSituacao::REPROVADO
        ];

        $faltaSituacoes = [
            1 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
            2 => App_Model_MatriculaSituacao::APROVADO,
            3 => App_Model_MatriculaSituacao::REPROVADO
        ];

        // Possibilidades do retorno do objeto
        $expected = [
            1 => [  //Aprova Andame Retido Recupe
                1 => [false, true, false, false],
                2 => [true, false, false, false],
                3 => [true, false, true, false]
            ],
            2 => [
                1 => [false, true, false, true],
                2 => [true, false, false, true],
                3 => [true, false, true, true]
            ],
            3 => [
                1 => [false, true, false, false],
                2 => [false, true, false, false],
                3 => [false, false, true, false]
            ],
            4 => [
                1 => [false, true, false, true],
                2 => [false, true, false, true],
                3 => [false, true, true, true]
            ],
            5 => [
                1 => [false, true, false, false],
                2 => [false, false, false, false],
                3 => [false, false, true, false]
            ]
        ];

        $expectedSituation = [
            App_Model_MatriculaSituacao::EM_ANDAMENTO,
            App_Model_MatriculaSituacao::APROVADO,
            App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS,
            App_Model_MatriculaSituacao::EM_EXAME,
            App_Model_MatriculaSituacao::APROVADO_APOS_EXAME,
            App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS,
            App_Model_MatriculaSituacao::EM_ANDAMENTO,
            App_Model_MatriculaSituacao::EM_ANDAMENTO,
            App_Model_MatriculaSituacao::REPROVADO,
            App_Model_MatriculaSituacao::EM_EXAME,
            App_Model_MatriculaSituacao::EM_EXAME,
            App_Model_MatriculaSituacao::EM_EXAME,
            App_Model_MatriculaSituacao::EM_ANDAMENTO,
            App_Model_MatriculaSituacao::REPROVADO,
            App_Model_MatriculaSituacao::REPROVADO,
        ];

        $currentSituation = 0;
        foreach ($notaSituacoes as $i => $notaSituacao) {
            $nota->situacao = $notaSituacao;

            foreach ($faltaSituacoes as $ii => $faltaSituacao) {
                $falta->situacao = $faltaSituacao;

                $service
                    ->method('getSituacaoFaltas')
                    ->willReturn($falta);

                $service
                    ->method('getSituacaoNotas')
                    ->willReturn($nota);

                $service
                    ->method('hasRegraAvaliacaoMediaRecuperacao')
                    ->willReturn(true);

                $service
                    ->method('getRegraAvaliacaoTipoNota')
                    ->willReturn(null);

                $situacao = $service->getSituacaoAluno();

                $this->assertEquals($expected[$i][$ii][0], $situacao->aprovado, "Aprovado, caso $i - $ii");
                $this->assertEquals($expected[$i][$ii][1], $situacao->andamento, "Andamento, caso $i - $ii");
                $this->assertEquals($expected[$i][$ii][2], $situacao->retidoFalta, "Retido por falta, caso $i - $ii");
                $this->assertEquals($expected[$i][$ii][3], $situacao->recuperacao, "Recuperação, caso $i - $ii");
                $this->assertEquals($expectedSituation[$currentSituation], $situacao->situacao, "Situação, caso $i - $ii");

                ++$currentSituation;
            }
        }
    }
}
