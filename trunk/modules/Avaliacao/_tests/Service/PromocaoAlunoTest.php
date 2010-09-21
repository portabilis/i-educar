<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Avaliacao/_tests/Service/TestCommon.php';

/**
 * Avaliacao_Service_PromocaoAlunoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_PromocaoAlunoTest extends Avaliacao_Service_TestCommon
{
  protected function _setUpRegraAvaliacaoMock($tipoProgressao)
  {
    $mock = $this->getCleanMock('RegraAvaliacao_Model_Regra');
    $mock->expects($this->at(0))
         ->method('get')
         ->with('tipoProgressao')
         ->will($this->returnValue($tipoProgressao));

    return $mock;
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testPromoverAlunoLancaExcecaoCasoSituacaoEstejaEmAndamento()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE;
    $situacao->andamento   = TRUE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->promover();
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testPromoverAlunoLancaExcecaoCasoMatriculaDoAlunoJaEstejaAprovadaOuReprovada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::APROVADO));

    $service->promover();
  }

  public function testPromoverAlunoAutomaticamenteProgressaoContinuada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::CONTINUADA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testPromoverAlunoAutomaticamenteProgressaoNaoContinuadaAutoMediaPresenca()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_MEDIA_PRESENCA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testReprovarAlunoAutomaticamenteProgressaoNaoContinuadaAutoMediaPresenca()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = TRUE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_MEDIA_PRESENCA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, FALSE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testPromoverAlunoAutomaticamenteProgressaoNaoContinuadaAutoMedia()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = TRUE;  // Não considera retenção por falta

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_SOMENTE_MEDIA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testPromoverAlunoManualmenteProgressaoNaoContinuadaLancaExcecaoSeNaoConfirmada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE; // Reprovado por nota
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    try {
      $service->promover();
      $this->fail('Invocar o método "->promover()" sem uma confirmação booleana '
                  . 'explícita (TRUE ou FALSE) em uma progressão "NAO_CONTINUADA_MANUAL" '
                  . 'causa exceção.');
    }
    catch (CoreExt_Service_Exception $e)
    {
    }
  }

  public function testPromoverAlunoManualmenteProgressaoNaoContinuada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE; // Reprovado por nota
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover(TRUE));
  }

  public function testReprovarAlunoManualmenteProgressaoNaoContinuada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE; // Reprovado por nota
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, FALSE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover(FALSE));
  }

  public function testSaveBoletim()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    $service = $this->setExcludedMethods(array('save'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('saveNotas')
            ->will($this->returnValue($service));

    $service->expects($this->at(1))
            ->method('saveFaltas')
            ->will($this->returnValue($service));

    $service->expects($this->at(2))
            ->method('savePareceres')
            ->will($this->returnValue($service));

    $service->expects($this->at(3))
            ->method('promover')
            ->will($this->returnValue(TRUE));

    try {
      $service->save();
    }
    catch (Exception $e) {
      $this->fail('O método "->save()" não deveria ter lançado exceção com o '
                  . 'cenário de teste configurado.');
    }
  }

  public function testIntegracaoMatriculaPromoverAluno()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::CONTINUADA);

    $service = $this->setExcludedMethods(array('promover', '_updateMatricula'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    // Configura mock de instância de classe legada
    $matricula = $this->getCleanMock('clsPmieducarMatricula');

    $matricula->expects($this->at(0))
              ->method('edita')
              ->will($this->returnValue(TRUE));

    CoreExt_Entity::addClassToStorage('clsPmieducarMatricula', $matricula,
      'include/pmieducar/clsPmieducarMatricula.inc.php', TRUE);

    $this->assertTrue($service->promover());
  }
}