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
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'ComponenteCurricular/Model/Componente.php';

/**
 * ComponenteCurricular_Model_ComponenteDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_ComponenteDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'ComponenteCurricular_Model_Componente';
  protected $_tableName   = 'componente_curricular';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'instituicao' => 'instituicao_id',
    'area_conhecimento' => 'area_conhecimento_id'
  );

  protected $_notPersistable = array(
    'cargaHoraria'
  );

  /**
   * @var AreaConhecimento_Model_AreaDataMapper
   */
  protected $_areaDataMapper = NULL;

  /**
   * @var ComponenteCurricular_Model_AnoEscolarDataMapper
   */
  protected $_anoEscolarDataMapper = NULL;

  /**
   * Setter.
   * @param  AreaConhecimento_Model_AreaDataMapper $mapper
   * @return ComponenteCurricular_Model_ComponenteDataMapper Provê interface fluída
   */
  public function setAreaDataMapper(AreaConhecimento_Model_AreaDataMapper $mapper)
  {
    $this->_areaDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return AreaConhecimento_Model_AreaDataMapper
   */
  public function getAreaDataMapper()
  {
    if (is_null($this->_areaDataMapper)) {
      require_once 'AreaConhecimento/Model/AreaDataMapper.php';
      $this->setAreaDataMapper(new AreaConhecimento_Model_AreaDataMapper());
    }
    return $this->_areaDataMapper;
  }

  /**
   * Setter.
   * @param ComponenteCurricular_Model_AnoEscolarDataMapper $mapper
   * @return ComponenteCurricular_Model_ComponenteCurricular Provê interface fluída
   */
  public function setAnoEscolarDataMapper(ComponenteCurricular_Model_AnoEscolarDataMapper $mapper)
  {
    $this->_anoEscolarDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return ComponenteCurricular_Model_AnoEscolarDataMapper
   */
  public function getAnoEscolarDataMapper()
  {
    if (is_null($this->_anoEscolarDataMapper)) {
      require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
      $this->setAnoEscolarDataMapper(new ComponenteCurricular_Model_AnoEscolarDataMapper());
    }
    return $this->_anoEscolarDataMapper;
  }

  /**
   * Finder.
   * @return array Array de objetos AreaConhecimento_Model_Area
   */
  public function findAreaConhecimento()
  {
    return $this->getAreaDataMapper()->findAll(array('nome'));
  }

  /**
   * @param int $componenteCurricular
   * @return ComponenteCurricular_Model_Componente
   */
  public function findComponenteCurricularAnoEscolar($componenteCurricular, $anoEscolar)
  {
    $anoEscolar = $this->getAnoEscolarDataMapper()->find(array($componenteCurricular, $anoEscolar));
    $componenteCurricular = $this->find($componenteCurricular);
    $componenteCurricular->cargaHoraria = $anoEscolar->cargaHoraria;
    return $componenteCurricular;
  }
}