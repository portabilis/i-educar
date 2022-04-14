<?php

abstract class Avaliacao_Service_FaltaCommon extends Avaliacao_Service_TestCommon
{
    /**
     * @return Avaliacao_Model_FaltaComponente
     */
    abstract protected function _getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim();

    /**
     * @return Avaliacao_Model_FaltaComponente
     */
    abstract protected function _getFaltaTestAdicionaFaltaNoBoletim();

    /**
     * Realiza asserções específicas para os validadores de uma instância de
     * Avaliacao_Model_FaltaAbstract
     */
    abstract protected function _testAdicionaFaltaNoBoletimVerificaValidadores(Avaliacao_Model_FaltaAbstract $falta);

    /**
     * @see Avaliacao_Service_FaltaCommon#_getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
     */
    public function testInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
    {
        $service = $this->_getServiceInstance();

        $falta = $this->_getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim();

        // Atribuição simples
        $service->addFalta($falta)
            ->addFalta($falta);

        $this->assertEquals(1, count($service->getFaltas()));

        // Via atribuição em lote
        $falta = clone $falta;
        $service->addFaltas([$falta, $falta, $falta]);

        $this->assertEquals(2, count($service->getFaltas()));
    }

    /**
     * @see Avaliacao_Service_FaltaCommon#_getFaltaTestAdicionaFaltaNoBoletim()
     * @see Avaliacao_Service_FaltaCommon#_testAdicionaFaltaNoBoletimVerificaValidadores()
     */
    public function testAdicionaFaltaNoBoletim()
    {
        $service = $this->_getServiceInstance();

        $falta = $this->_getFaltaTestAdicionaFaltaNoBoletim();

        $faltaOriginal = clone $falta;
        $service->addFalta($falta);

        $faltas = $service->getFaltas();
        $serviceFalta = array_shift($faltas);

        // Valores declarados explicitamente, verificação explícita
        $this->assertEquals($faltaOriginal->quantidade, $serviceFalta->quantidade);

        // Valores populados pelo service
        $this->assertNotEquals($faltaOriginal->etapa, $serviceFalta->etapa);

        // Validadores injetados no objeto
        $this->_testAdicionaFaltaNoBoletimVerificaValidadores($serviceFalta);
    }
}
