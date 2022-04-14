<?php

class Avaliacao_Service_ParecerDescritivoGeralAnualTest extends Avaliacao_Service_ParecerDescritivoCommon
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

    protected function _getTestSalvarPareceresNoBoletimInstanciasDePareceres()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ])
        ];
    }

    protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ])
        ];
    }

    protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ])
        ];
    }

    protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.'
            ])
        ];
    }

    protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()
    {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An'
            ])
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

    public function testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
    {
        $this->markTestSkipped();
    }
}
