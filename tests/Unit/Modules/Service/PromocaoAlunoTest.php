<?php

use PHPUnit\Framework\MockObject\MockObject;

class Avaliacao_Service_PromocaoAlunoTest extends Avaliacao_Service_TestCommon
{
    protected function _setUpRegraAvaliacaoMock($tipoProgressao)
    {
        $mock = $this->getCleanMock('RegraAvaliacao_Model_Regra');
        $mock
            ->method('get')
            ->with('tipoProgressao')
            ->willReturn($tipoProgressao);

        return $mock;
    }

    public function testPromoverAlunoLancaExcecaoCasoSituacaoEstejaEmAndamento()
    {
        $situacao = new stdClass();
        $situacao->aprovado = true;
        $situacao->andamento = true;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = false;

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(
                [
                    'promover',
                    '_updateMatricula'
                ]
            )
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service->expects(self::once())
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getOption')
            ->willReturnMap(
                [
                    ['aprovado', App_Model_MatriculaSituacao::EM_ANDAMENTO],
                    ['matricula', 1]
                ]
            );

        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(0);

        $this->expectException('CoreExt_Service_Exception');
        $service->promover();
    }

    public function testPromoverAlunoAutomaticamenteProgressaoContinuada()
    {
        $situacao = new stdClass();
        $situacao->aprovado = true;
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = false;

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['promover'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        // Mock para RegraAvaliacao_Model_Regra
        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(RegraAvaliacao_Model_TipoProgressao::CONTINUADA);

        $service
            ->expects(self::once())
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getOption')
            ->willReturnMap(
                [
                    ['aprovado', App_Model_MatriculaSituacao::EM_ANDAMENTO]
                ]
            );

        $service
            ->expects(self::once())
            ->method('_updateMatricula')
            ->willReturn(true);

        self::assertTrue($service->promover());
    }

    public function testPromoverAlunoAutomaticamenteProgressaoNaoContinuadaAutoMediaPresenca()
    {
        $situacao = new stdClass();
        $situacao->aprovado = true;
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = false;
        $situacao->aprovadoComDependencia = false;

        $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
        $codUsuario = $this->_getConfigOption('usuario', 'cod_usuario');

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['promover'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA);

        $service
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getOption')
            ->willReturnMap(
                [
                    ['matricula', $codMatricula],
                    ['usuario', $codUsuario]
                ]
            );

        $service
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, true)
            ->willReturn(true);

        self::assertTrue($service->promover());
    }

    public function testReprovarAlunoAutomaticamenteProgressaoNaoContinuadaAutoMediaPresenca()
    {
        $situacao = new stdClass();
        $situacao->aprovado = true;
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = true;

        $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
        $codUsuario = $this->_getConfigOption('usuario', 'cod_usuario');

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['promover'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA);

        $service
            ->method('getOption')
            ->willReturnMap([
                ['matricula', $codMatricula],
                ['usuario', $codUsuario]
            ]);

        $service
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS)
            ->willReturn(true);

        self::assertTrue($service->promover());
    }

    public function testPromoverAlunoAutomaticamenteProgressaoNaoContinuadaAutoMedia()
    {
        $situacao = new stdClass();
        $situacao->aprovado = true;
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = true;  // Não considera retenção por falta
        $situacao->aprovadoComDependencia = false;

        $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
        $codUsuario = $this->_getConfigOption('usuario', 'cod_usuario');

        // Mock para RegraAvaliacao_Model_Regra
        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['promover'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_SOMENTE_MEDIA);

        $service
            ->method('getOption')
            ->willReturnMap(
                [
                    ['matricula', $codMatricula],
                    ['usuario', $codUsuario]
                ]
            );

        $service
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, true)
            ->willReturn(true);

        self::assertTrue($service->promover());
    }

    public function testPromoverAlunoManualmenteProgressaoNaoContinuada()
    {
        $situacao = new stdClass();
        $situacao->aprovado = false; // Reprovado por nota
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = false;

        $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
        $codUsuario = $this->_getConfigOption('usuario', 'cod_usuario');

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['promover'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

        $service
            ->method('getOption')
            ->willReturnMap(
                [
                    ['matricula', $codMatricula],
                    ['usuario', $codUsuario]
                ]
            );

        $service
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, true)
            ->willReturn(true);

        self::assertTrue($service->promover(true));
    }

    public function testReprovarAlunoManualmenteProgressaoNaoContinuada()
    {
        $situacao = new stdClass();
        $situacao->aprovado = false; // Reprovado por nota
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = false;

        $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
        $codUsuario = $this->_getConfigOption('usuario', 'cod_usuario');

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['promover'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

        $service
            ->method('getOption')
            ->willReturnMap(
                [
                    ['matricula', $codMatricula],
                    ['usuario', $codUsuario]
                ]
            );

        $service
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, App_Model_MatriculaSituacao::REPROVADO)
            ->willReturn(true);

        self::assertTrue($service->promover());
    }

    public function testSaveBoletim()
    {
        $situacao = new stdClass();
        $situacao->aprovado = true;
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = false;

        $this->_getConfigOption('matricula', 'cod_matricula');
        $this->_getConfigOption('usuario', 'cod_usuario');

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['save'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service
            ->method('saveNotas')
            ->willReturn($service);

        $service
            ->method('saveFaltas')
            ->willReturn($service);

        $service
            ->method('savePareceres')
            ->willReturn($service);

        $service
            ->method('promover')
            ->willReturn(true);

        try {
            $service->save();
        } catch (Exception $e) {
            $this->fail('O método "->save()" não deveria ter lançado exceção com o '
                . 'cenário de teste configurado.');
        }

        self::assertTrue(true, 'O método "->save()" foi executado com sucesso');
    }

    public function testIntegracaoMatriculaPromoverAluno()
    {
        $situacao = new stdClass();
        $situacao->aprovado = true;
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->retidoFalta = false;

        $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
        $codUsuario = $this->_getConfigOption('usuario', 'cod_usuario');

        /** @var Avaliacao_Service_Boletim|MockObject $service */
        $service = $this
            ->setExcludedMethods(['promover', '_updateMatricula'])
            ->getCleanMock('Avaliacao_Service_Boletim');

        $service
            ->method('getSituacaoAluno')
            ->willReturn($situacao);

        $service
            ->method('getRegraAvaliacaoTipoProgressao')
            ->willReturn(RegraAvaliacao_Model_TipoProgressao::CONTINUADA);

        $service
            ->method('getOption')
            ->willReturnMap(
                [
                    ['matricula', $codMatricula],
                    ['usuario', $codUsuario]
                ]
            );

        // Configura mock de instância de classe legada
        /** @var clsPmieducarMatricula|MockObject $matricula */
        $matricula = $this->getCleanMock('clsPmieducarMatricula');

        $matricula
            ->method('edita')
            ->willReturn(true);

        CoreExt_Entity::addClassToStorage(
            'clsPmieducarMatricula',
            $matricula,
            'include/pmieducar/clsPmieducarMatricula.inc.php',
            true
        );

        self::assertTrue($service->promover());
    }
}
