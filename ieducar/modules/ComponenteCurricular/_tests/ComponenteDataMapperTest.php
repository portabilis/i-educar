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

require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

/**
 * ComponenteDataMapperTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ComponenteDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new ComponenteCurricular_Model_ComponenteDataMapper();
  }

  public function testGetterDeAreaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('AreaConhecimento_Model_AreaDataMapper', $this->_mapper->getAreaDataMapper());
  }

  public function testGetterDeAnoEscolarDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('ComponenteCurricular_Model_AnoEscolarDataMapper',
      $this->_mapper->getAnoEscolarDataMapper());
  }

  public function testFindAreaConhecimento()
  {
    // Valores de retorno
    $returnValue = array(new AreaConhecimento_Model_Area(array('id' => 1, 'nome' => 'Ciências exatas')));

    // Mock para área de conhecimento
    $mock = $this->getCleanMock('AreaConhecimento_Model_AreaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padrão pelo mock
    $this->_mapper->setAreaDataMapper($mock);
    $areas = $this->_mapper->findAreaConhecimento();

    $this->assertEquals($returnValue, $areas);
  }

  public function testFindComponenteCurricularAnoEscolar()
  {
    // Valores de retorno
    $returnValue = new ComponenteCurricular_Model_Componente(
      array('id' => 1, 'nome' => 'Ciências exatas', 'cargaHoraria' => 100)
    );

    $returnAnoEscolar = new ComponenteCurricular_Model_AnoEscolar(array(
      'componenteCurricular' => 1, 'anoEscolar' => 1, 'cargaHoraria' => 100
    ));

    // Mock para Ano Escolar
    $mock = $this->getCleanMock('ComponenteCurricular_Model_AnoEscolarDataMapper');
    $mock->expects($this->once())
         ->method('find')
         ->with(array(1, 1))
         ->will($this->returnValue($returnAnoEscolar));

    // Mock para Componente, exclui um método de ser mocked
    $mapper = $this->setExcludedMethods(array('findComponenteCurricularAnoEscolar'))
                   ->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');

    // O método find do mapper será chamado uma vez
    $mapper->expects($this->once())
           ->method('find')
           ->with(1)
           ->will($this->returnValue($returnValue));

    // Como um mock não mantém estado, força o retorno do mapper AnoEscolarDataMapper mocked
    $mapper->expects($this->once())
           ->method('getAnoEscolarDataMapper')
           ->will($this->returnValue($mock));

    // Chama o método
    $componenteCurricular = $mapper->findComponenteCurricularAnoEscolar(1, 1);

    $this->assertEquals($returnValue, $componenteCurricular);
  }
}