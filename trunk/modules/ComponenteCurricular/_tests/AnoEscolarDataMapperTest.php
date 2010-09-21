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
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';

/**
 * AnoEscolarDataMapperTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class AnoEscolarDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper($this->getDbMock());
  }

  public function testGetterDeComponenteCurricularMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('ComponenteCurricular_Model_ComponenteDataMapper', $this->_mapper->getComponenteDataMapper());
  }

  public function testFindComponentePorCurso()
  {
    // Valores de retorno
    $expected = array(
      new ComponenteCurricular_Model_Componente(array('id' => 1, 'nome' => 'Matemática')),
      new ComponenteCurricular_Model_Componente(array('id' => 2, 'nome' => 'Português'))
    );

    // Valores de retorno para o mock do adapter
    $returnValues = array(
      0 => array('componente_curricular_id' => 1),
      1 => array('componente_curricular_id' => 2)
    );

    // Configura mock para retornar um array de IDs de componentes
    $dbMock = $this->getDbMock();

    $dbMock->expects($this->any())
           ->method('ProximoRegistro')
           ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $dbMock->expects($this->any())
           ->method('Tupla')
           ->will($this->onConsecutiveCalls($returnValues[0], $returnValues[1]));

    // Mock para área de conhecimento
    $mock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
    $mock->expects($this->any())
         ->method('find')
         ->will($this->onConsecutiveCalls($expected[0], $expected[1]));

    // Substitui o data mapper padrão pelo mock
    $this->_mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper($dbMock);
    $this->_mapper->setComponenteDataMapper($mock);
    $componentes = $this->_mapper->findComponentePorCurso(1);

    $this->assertEquals($expected, $componentes);
  }

  public function testFindComponentePorSerie()
  {
    // Valores de retorno
    $expected = array(
      1 => new ComponenteCurricular_Model_Componente(array('id' => 1, 'nome' => 'Matemática')),
      2 => new ComponenteCurricular_Model_Componente(array('id' => 2, 'nome' => 'Português'))
    );

    // Valores de retorno para o mock do adapter
    $returnValues = array(
      0 => array('componente_curricular_id' => 1, 'ano_escolar_id' => 1),
      1 => array('componente_curricular_id' => 2, 'ano_escolar_id' => 1)
    );

    // Configura mock para retornar um array de IDs de componentes
    $dbMock = $this->getDbMock();

    $dbMock->expects($this->any())
           ->method('ProximoRegistro')
           ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $dbMock->expects($this->any())
           ->method('Tupla')
           ->will($this->onConsecutiveCalls($returnValues[0], $returnValues[1]));

    // Mock para área de conhecimento
    $mock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
    $mock->expects($this->any())
         ->method('find')
         ->will($this->onConsecutiveCalls($expected[1], $expected[2]));

    // Substitui o data mapper padrão pelo mock
    $this->_mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper($dbMock);
    $this->_mapper->setComponenteDataMapper($mock);
    $componentes = $this->_mapper->findComponentePorSerie(1);

    $this->assertEquals($expected, $componentes);
  }
}