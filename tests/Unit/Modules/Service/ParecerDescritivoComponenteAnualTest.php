<?php

class Avaliacao_Service_ParecerDescritivoComponenteAnualTest extends Avaliacao_Service_ParecerDescritivoCommon
{
    protected function setUp(): void
    {
        $this->_setRegraOption('parecerDescritivo', RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);
        parent::setUp();
    }

    protected function _getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
    {
        return new Avaliacao_Model_ParecerDescritivoComponente([
            'componenteCurricular' => 1,
            'parecer' => 'Ok.'
        ]);
    }

    protected function _getTestAdicionaParecerNoBoletim()
    {
        return new Avaliacao_Model_ParecerDescritivoComponente([
            'componenteCurricular' => 1,
            'parecer' => 'N/D.'
        ]);
    }

    protected function _getTestSalvarPareceresNoBoletimInstanciasDePareceres()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 2,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 3,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 4,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ])
        ];
    }

    protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 2,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ])
        ];
    }

    protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'id' => 1,
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ])
        ];
    }

    protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 2,
                'parecer' => 'N/D.',
            ])
        ];
    }

    protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'id' => 1,
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ]),
        ];
    }

    protected function _testAdicionaParecerNoBoletimVerificaValidadores(Avaliacao_Model_ParecerDescritivoAbstract $parecer)
    {
        $this->markTestSkipped();
        $this->assertEquals(1, $parecer->get('componenteCurricular'));
        $this->assertEquals('An', $parecer->etapa);
        $this->assertEquals('N/D.', $parecer->parecer);

        $validators = $parecer->getValidatorCollection();

        $this->assertEquals($this->_getEtapasPossiveisParecer(), $validators['etapa']->getOption('choices'));

        $this->assertEquals(
            $this->_getComponentesCursados(),
            array_values($validators['componenteCurricular']->getOption('choices'))
        );
    }

    public function testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
    {
        $this->markTestSkipped();
    }
}
