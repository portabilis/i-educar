<?php

class Avaliacao_Service_ParecerDescritivoGeralEtapaTest extends Avaliacao_Service_ParecerDescritivoCommon
{
    protected function setUp(): void
    {
        $this->_setRegraOption('parecerDescritivo', RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL);
        parent::setUp();
    }

    protected function _getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
    {
        return new Avaliacao_Model_ParecerDescritivoGeral([
            'parecer' => 'Ok.'
        ]);
    }

    protected function _getTestAdicionaParecerNoBoletim()
    {
        return new Avaliacao_Model_ParecerDescritivoGeral([
            'parecer' => 'N/D.'
        ]);
    }

    protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 2
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 3
            ])
        ];
    }

    protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 1
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 2
            ])
        ];
    }

    protected function _getTestSalvarPareceresNoBoletimInstanciasDePareceres()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 1,
                'parecer' => 'N/D.'
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 2,
                'parecer' => 'N/D.'
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 3,
                'parecer' => 'N/D.'
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 4,
                'parecer' => 'N/D.'
            ])
        ];
    }

    protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 4
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
            ])
        ];
    }

    protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 2
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 2,
                'parecer' => 'N/D.',
                'etapa' => 2
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 3,
                'parecer' => 'N/D.',
                'etapa' => 3
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 4,
                'parecer' => 'N/D.',
                'etapa' => 4
            ]),
        ];
    }

    protected function _testAdicionaParecerNoBoletimVerificaValidadores(Avaliacao_Model_ParecerDescritivoAbstract $parecer)
    {
        $this->markTestSkipped();

        $this->assertEquals(1, $parecer->etapa);
        $this->assertEquals('N/D.', $parecer->parecer);

        $validators = $parecer->getValidatorCollection();

        $this->assertEquals($this->_getEtapasPossiveisParecer(), $validators['etapa']->getOption('choices'));
        $this->assertFalse(isset($validators['componenteCurricular']));
    }
}
