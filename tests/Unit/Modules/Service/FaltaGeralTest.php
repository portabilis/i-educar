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

require_once __DIR__.'/FaltaCommon.php';

/**
 * Avaliacao_Service_FaltaGeralTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaGeralTest extends Avaliacao_Service_FaltaCommon
{
  protected function setUp(): void
  {
    $this->_setRegraOption('tipoPresenca', RegraAvaliacao_Model_TipoPresenca::GERAL);
    parent::setUp();
  }

  protected function _getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
  {
    return new Avaliacao_Model_FaltaGeral(array(
      'quantidade' => 10
    ));
  }

  protected function _getFaltaTestAdicionaFaltaNoBoletim()
  {
    return new Avaliacao_Model_FaltaComponente(array(
      'quantidade'           => 10
    ));
  }

  protected function _testAdicionaFaltaNoBoletimVerificaValidadores(Avaliacao_Model_FaltaAbstract $falta)
  {
    $this->markTestSkipped();

    $this->assertEquals(1, $falta->etapa);
    $this->assertEquals(10, $falta->quantidade);

    $validators = $falta->getValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);
    $this->assertFalse(isset($validators['componenteCurricular']));

    // Opções dos validadores

    // Etapas possíveis para o lançamento de nota
    $this->assertEquals(
      array_merge(range(1, count($this->_getConfigOptions('anoLetivoModulo'))), array('Rc')),
      $validators['etapa']->getOption('choices')
    );
  }

  /**
   * Testa o service adicionando faltas de apenas um componente curricular,
   * para todas as etapas regulares (1 a 4).
   */
  public function testSalvarFaltasNoBoletim()
  {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 7,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 11,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 8,
        'etapa'      => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 8,
        'etapa'      => 4
      )),
    );

    // Configura mock para Avaliacao_Model_FaltaGeralDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($faltas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($faltas[1])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(3))
         ->method('save')
         ->with($faltas[2])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(4))
         ->method('save')
         ->with($faltas[3])
         ->will($this->returnValue(TRUE));

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();
  }

  /**
   * Testa o service adicionando novas faltas para um componente curricular,
   * que inclusive já tem a falta lançada para a segunda etapa.
   */
  public function testSalvasFaltasNoBoletimComEtapasLancadas()
  {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 7,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 9,
        'etapa'      => 3
      ))
    );

    $faltasPersistidas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 1,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 8,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 11,
        'etapa'      => 2
      ))
    );

    // Configura mock para Avaliacao_Model_FaltaGeralDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($faltasPersistidas));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($faltas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($faltas[1])
         ->will($this->returnValue(TRUE));

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();
  }

  public function testSalvasFaltasAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
  {
    $this->markTestSkipped();

    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 7,
        'etapa'      => 2
      )),
      // Etapa omitida, será atribuída a etapa '3'
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 9
      ))
    );

    $faltasPersistidas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 1,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 8,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 11,
        'etapa'      => 2
      ))
    );

    // Configura mock para Avaliacao_Model_FaltaGeralDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($faltasPersistidas));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($faltas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($faltas[1])
         ->will($this->returnValue(TRUE));

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();

    $faltas = $service->getFaltas();

    $falta = array_shift($faltas);
    $this->assertEquals(2, $falta->etapa);

    // Etapa atribuída automaticamente
    $falta = array_shift($faltas);
    $this->assertEquals(3, $falta->etapa);
  }
}
