<?php

abstract class Avaliacao_Service_FaltaCommon extends Avaliacao_Service_TestCommon
{
    /**
     * @var Closure(): Avaliacao_Model_FaltaComponente
     */
    protected Closure $getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim;

    /**
     * @var Closure(): Avaliacao_Model_FaltaComponente
     */
    protected Closure $getFaltaTestAdicionaFaltaNoBoletim;

    /**
     * Realiza asserções específicas para os validadores de uma instância de
     * Avaliacao_Model_FaltaAbstract
     *
     * @var Closure(Avaliacao_Model_FaltaAbstract): void
     */
    protected Closure $testAdicionaFaltaNoBoletimVerificaValidadores;

    /**
     * Registra os testes comuns aos arquivos que usam esta base. O Pest
     * não executa métodos de teste herdados.
     */
    public static function registerFaltaTests(): void
    {
        /**
         * @see Avaliacao_Service_FaltaCommon::$getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim
         */
        test('instancia de falta e registrada apenas uma vez no boletim', function () {
            $service = $this->_getServiceInstance();

            $falta = ($this->getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim)();

            // Atribuição simples
            $service->addFalta($falta)
                ->addFalta($falta);

            $this->assertEquals(1, count($service->getFaltas()));

            // Via atribuição em lote
            $falta = clone $falta;
            $service->addFaltas([$falta, $falta, $falta]);

            $this->assertEquals(2, count($service->getFaltas()));
        });

        /**
         * @see Avaliacao_Service_FaltaCommon::$getFaltaTestAdicionaFaltaNoBoletim
         * @see Avaliacao_Service_FaltaCommon::$testAdicionaFaltaNoBoletimVerificaValidadores
         */
        test('adiciona falta no boletim', function () {
            $service = $this->_getServiceInstance();

            $falta = ($this->getFaltaTestAdicionaFaltaNoBoletim)();

            $faltaOriginal = clone $falta;
            $service->addFalta($falta);

            $faltas = $service->getFaltas();
            $serviceFalta = array_shift($faltas);

            // Valores declarados explicitamente, verificação explícita
            $this->assertEquals($faltaOriginal->quantidade, $serviceFalta->quantidade);

            // Valores populados pelo service
            $this->assertNotEquals($faltaOriginal->etapa, $serviceFalta->etapa);

            // Validadores injetados no objeto
            ($this->testAdicionaFaltaNoBoletimVerificaValidadores)($serviceFalta);
        });
    }
}
