<?php

/**
 * Class Avaliacao_Service_ParecerDescritivoCommon
 */
abstract class Avaliacao_Service_ParecerDescritivoCommon extends Avaliacao_Service_TestCommon
{
    /**
     * Retorna as etapas possíveis para a instância do parecer.
     *
     * @return array
     */
    protected function _getEtapasPossiveisParecer()
    {
        $parecerDescritivo = $this->_getRegraOption('parecerDescritivo');

        $anuais = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
        ];

        if (in_array($parecerDescritivo, $anuais, true)) {
            return ['An'];
        }

        return $this->_getEtapasPossiveis();
    }

    /**
     * Retorna o nome da classe CoreExt_DataMapper correta de acordo com a
     * configuração da regra. Método auxiliar para criação de mocks.
     *
     * @return string
     */
    protected function _getParecerDescritivoDataMapper()
    {
        $parecerDescritivo = $this->_getRegraOption('parecerDescritivo');

        switch ($parecerDescritivo) {
            case RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL:
            case RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL:
                $mapper = 'Avaliacao_Model_ParecerDescritivoGeralDataMapper';

                break;
            case RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE:
            case RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE:
                $mapper = 'Avaliacao_Model_ParecerDescritivoComponenteDataMapper';

                break;
        }

        return $mapper;
    }

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(): Avaliacao_Model_ParecerDescritivoAbstract
     */
    protected Closure $getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim;

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(): Avaliacao_Model_ParecerDescritivoAbstract
     */
    protected Closure $getTestAdicionaParecerNoBoletim;

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(Avaliacao_Model_ParecerDescritivoAbstract): void
     */
    protected Closure $testAdicionaParecerNoBoletimVerificaValidadores;

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(): array
     */
    protected Closure $getTestSalvarPareceresNoBoletimInstanciasDePareceres;

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(): array
     */
    protected Closure $getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias;

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(): array
     */
    protected Closure $getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas;

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(): array
     */
    protected Closure $getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias;

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests()
     *
     * @var Closure(): array
     */
    protected Closure $getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas;

    /**
     * Registra os testes comuns aos arquivos que usam esta base. O Pest
     * não executa métodos de teste herdados.
     */
    public static function registerParecerDescritivoTests(): void
    {
        /**
         * @see Avaliacao_Service_ParecerDescritivoCommon::$getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim
         */
        test('instancia de parecer e registrada apenas uma vez no boletim', function () {
            $service = $this->_getServiceInstance();

            $parecer = ($this->getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim)();

            $service
                ->addParecer($parecer)
                ->addParecer($parecer);

            self::assertCount(1, $service->getPareceres());

            $parecer = clone $parecer;
            $service->addPareceres([$parecer, $parecer, $parecer]);

            self::assertCount(2, $service->getPareceres());
        });

        /**
         * @see Avaliacao_Service_ParecerDescritivoCommon::$getTestAdicionaParecerNoBoletim
         * @see Avaliacao_Service_ParecerDescritivoCommon::$testAdicionaParecerNoBoletimVerificaValidadores
         */
        test('adiciona parecer no boletim', function () {
            $service = $this->_getServiceInstance();

            $parecer = ($this->getTestAdicionaParecerNoBoletim)();

            $parecerOriginal = clone $parecer;
            $service->addParecer($parecer);

            $pareceres = $service->getPareceres();
            $serviceParecer = array_shift($pareceres);

            ($this->testAdicionaParecerNoBoletimVerificaValidadores)($serviceParecer);
        });

        /**
         * @see Avaliacao_Service_ParecerDescritivoCommon::$getTestSalvarPareceresNoBoletimInstanciasDePareceres
         */
        test('salvar pareceres no boletim', function () {
            $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

            $pareceres = ($this->getTestSalvarPareceresNoBoletimInstanciasDePareceres)();

            // Configura mock para Avaliacao_Model_Parecer
            $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

            $mock
                ->method('findAll')
                ->with([], ['parecerDescritivoAluno' => $parecerAluno->id], ['etapa' => 'ASC'])
                ->willReturn([]);

            if (count($pareceres) === 4) {
                $mock
                    ->expects(self::exactly(4))
                    ->method('save')
                    ->willReturnOnConsecutiveCalls(true, true, true, true);
                $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[0]);
                $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[1]);
                $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[2]);
                $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[3]);
            }
            if (count($pareceres) === 1) {
                $mock
                    ->expects(self::once())
                    ->method('save')
                    ->willReturnOnConsecutiveCalls(true);
                $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[0]);
            }

            $this->_setParecerDescritivoAbstractDataMapperMock($mock);

            $service = $this->_getServiceInstance();
            $service->addPareceres($pareceres);
            $service->savePareceres();
        });

        /**
         * @see Avaliacao_Service_ParecerDescritivoCommon::$getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias
         * @see Avaliacao_Service_ParecerDescritivoCommon::$getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas
         */
        test('salvar pareceres no boletim com etapas ja lancadas', function () {
            $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

            $pareceres = ($this->getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias)();

            // Configura mock para Avaliacao_Model_Parecer
            $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

            $mock
                ->method('findAll')
                ->with([], ['parecerDescritivoAluno' => $parecerAluno->id], ['etapa' => 'ASC'])
                ->willReturn(($this->getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas)());
            if (count($pareceres) === 2) {
                $mock
                    ->expects(self::exactly(2))
                    ->method('save')
                    ->willReturnOnConsecutiveCalls(true, true);
                $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[0]);
                $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[1]);
            }
            if (count($pareceres) === 1) {
                $mock
                    ->expects(self::once())
                    ->method('save')
                    ->willReturnOnConsecutiveCalls(true);
                $this->assertIsArray($pareceres);
            }

            $this->_setParecerDescritivoAbstractDataMapperMock($mock);

            $service = $this->_getServiceInstance();
            $service->addPareceres($pareceres);
            $service->savePareceres();
        });

        /**
         * @see Avaliacao_Service_ParecerDescritivoCommon::$getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias
         * @see Avaliacao_Service_ParecerDescritivoCommon::$getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas
         */
        test('salvas pareceres atualizando etapa da ultima instancia adicionada no boletim com etapas lancadas', function () {
            $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

            $pareceres = ($this->getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias)();

            // Configura mock para Avaliacao_Model_Parecer
            $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

            $mock
                ->method('findAll')
                ->with([], ['parecerDescritivoAluno' => $parecerAluno->id], ['etapa' => 'ASC'])
                ->willReturn(($this->getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas)());
            $mock
                ->expects(self::exactly(2))
                ->method('save')
                ->willReturnOnConsecutiveCalls(true, true);

            if (count($pareceres) !== 2) {
                $this->markTestSkipped();
            }

            $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[0]);
            $this->assertInstanceOf('Avaliacao_Model_ParecerDescritivoAbstract', $pareceres[1]);

            $this->_setParecerDescritivoAbstractDataMapperMock($mock);

            $service = $this->_getServiceInstance();
            $service->addPareceres($pareceres);
            $service->savePareceres();
        });
    }
}
