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
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
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
     * @see Avaliacao_Service_ParecerDescritivoCommon#testInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
     *
     * @return Avaliacao_Model_ParecerDescritivoAbstract
     */
    abstract protected function _getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim();

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#testAdicionaParecerNoBoletim()
     *
     * @return Avaliacao_Model_ParecerDescritivoAbstract
     */
    abstract protected function _getTestAdicionaParecerNoBoletim();

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#testAdicionaParecerNoBoletim()
     *
     * @param Avaliacao_Model_ParecerDescritivoAbstract $parecer
     *
     */
    abstract protected function _testAdicionaParecerNoBoletimVerificaValidadores(Avaliacao_Model_ParecerDescritivoAbstract $parecer);

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvarPareceresNoBoletim()
     *
     * @return array
     */
    abstract protected function _getTestSalvarPareceresNoBoletimInstanciasDePareceres();

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvarPareceresNoBoletimComEtapasJaLancadas()
     *
     * @return array
     */
    abstract protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias();

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvarPareceresNoBoletimComEtapasJaLancadas()
     *
     * @return array
     */
    abstract protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas();

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
     *
     * @return array
     */
    abstract protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias();

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
     *
     * @return array
     */
    abstract protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas();

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
     */
    public function testInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
    {
        $service = $this->_getServiceInstance();

        $parecer = $this->_getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim();

        $service
            ->addParecer($parecer)
            ->addParecer($parecer);

        self::assertCount(1, $service->getPareceres());

        $parecer = clone $parecer;
        $service->addPareceres([$parecer, $parecer, $parecer]);

        self::assertCount(2, $service->getPareceres());
    }

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestAdicionaParecerNoBoletim()
     * @see Avaliacao_Service_ParecerDescritivoCommon#_testAdicionaParecerNoBoletimVerificaValidadores()
     */
    public function testAdicionaParecerNoBoletim()
    {
        $service = $this->_getServiceInstance();

        $parecer = $this->_getTestAdicionaParecerNoBoletim();

        $parecerOriginal = clone $parecer;
        $service->addParecer($parecer);

        $pareceres = $service->getPareceres();
        $serviceParecer = array_shift($pareceres);

        $this->_testAdicionaParecerNoBoletimVerificaValidadores($serviceParecer);
    }

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvarPareceresNoBoletimInstanciasDePareceres()
     */
    public function testSalvarPareceresNoBoletim()
    {
        $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

        $pareceres = $this->_getTestSalvarPareceresNoBoletimInstanciasDePareceres();

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
                ->withConsecutive(
                    [$pareceres[0]],
                    [$pareceres[1]],
                    [$pareceres[2]],
                    [$pareceres[3]]
                )
                ->willReturnOnConsecutiveCalls(true, true, true, true);
        }

        if (count($pareceres) === 1) {
            $mock
                ->expects(self::once())
                ->method('save')
                ->withConsecutive(
                    [$pareceres[0]],
                )
                ->willReturnOnConsecutiveCalls(true);
        }

        $this->_setParecerDescritivoAbstractDataMapperMock($mock);

        $service = $this->_getServiceInstance();
        $service->addPareceres($pareceres);
        $service->savePareceres();
    }

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias()
     * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()
     */
    public function testSalvarPareceresNoBoletimComEtapasJaLancadas()
    {
        $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

        $pareceres = $this->_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias();

        // Configura mock para Avaliacao_Model_Parecer
        $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

        $mock
            ->method('findAll')
            ->with([], ['parecerDescritivoAluno' => $parecerAluno->id], ['etapa' => 'ASC'])
            ->willReturn($this->_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas());

        if (count($pareceres) === 2) {
            $mock
                ->expects(self::exactly(2))
                ->method('save')
                ->withConsecutive(
                    [$pareceres[0]],
                    [$pareceres[1]]
                )
                ->willReturnOnConsecutiveCalls(true, true);
        }

        if (count($pareceres) === 1) {
            $mock
                ->expects(self::once())
                ->method('save')
                ->withConsecutive(
                    $pareceres
                )
                ->willReturnOnConsecutiveCalls(true);
        }

        $this->_setParecerDescritivoAbstractDataMapperMock($mock);

        $service = $this->_getServiceInstance();
        $service->addPareceres($pareceres);
        $service->savePareceres();
    }

    /**
     * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias()
     * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()
     */
    public function testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
    {
        $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

        $pareceres = $this->_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias();

        // Configura mock para Avaliacao_Model_Parecer
        $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

        $mock
            ->method('findAll')
            ->with([], ['parecerDescritivoAluno' => $parecerAluno->id], ['etapa' => 'ASC'])
            ->willReturn($this->_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas());

        $mock
            ->expects(self::exactly(2))
            ->method('save')
            ->withConsecutive(
                [$pareceres[0]],
                [$pareceres[1]]
            )
            ->willReturnOnConsecutiveCalls(true, true);

        $this->_setParecerDescritivoAbstractDataMapperMock($mock);

        $service = $this->_getServiceInstance();
        $service->addPareceres($pareceres);
        $service->savePareceres();
    }

    public function tearDown(): void
    {
        Portabilis_Utils_Database::$_db = null;
    }
}
