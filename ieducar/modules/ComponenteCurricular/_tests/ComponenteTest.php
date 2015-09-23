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

require_once 'ComponenteCurricular/Model/Componente.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';
require_once 'include/pmieducar/clsPmieducarSerie.inc.php';

/**
 * ComponenteTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ComponenteTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new ComponenteCurricular_Model_Componente();
  }

  public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('ComponenteCurricular_Model_ComponenteDataMApper', $this->_entity->getDataMapper());
  }

  public function testEntityValidators()
  {
    // Valores de retorno
    $returnValue = array(array('cod_instituicao' => 1, 'nm_instituicao' => 'Instituição'));
    $areaReturnValue = array(new AreaConhecimento_Model_Area(array('id' => 1, 'nome' => 'Ciências exatas')));

    // Mock para instituição
    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->once())
         ->method('lista')
         ->will($this->returnValue($returnValue));

    // Mock para área de conhecimento
    $areaConhecimentoMock = $this->getCleanMock('AreaConhecimento_Model_AreaDataMapper');
    $areaConhecimentoMock->expects($this->once())
                         ->method('findAll')
                         ->will($this->returnValue($areaReturnValue));

    // Registra a instância no repositório de classes de CoreExt_Entity
    $instance = ComponenteCurricular_Model_Componente::addClassToStorage(
      'clsPmieducarInstituicao', $mock);

    // Substitui o data mapper padrão pelo mock
    $this->_entity->getDataMapper()->setAreaDataMapper($areaConhecimentoMock);

    // Recupera os objetos CoreExt_Validate
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_Choice', $validators['instituicao']);
    $this->assertType('CoreExt_Validate_String', $validators['nome']);
    $this->assertType('CoreExt_Validate_String', $validators['abreviatura']);
    $this->assertType('CoreExt_Validate_Choice', $validators['tipo_base']);
    $this->assertType('CoreExt_Validate_Choice', $validators['area_conhecimento']);
  }
}