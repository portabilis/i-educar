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

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'ComponenteCurricular/Model/TipoBase.php';

/**
 * ComponenteCurricular_Model_Componente class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_Componente extends CoreExt_Entity
{
  protected $_data = array(
    'instituicao' => NULL,
    'nome' => NULL,
    'abreviatura' => NULL,
    'tipo_base' => NULL,
    'area_conhecimento' => NULL,
    'cargaHoraria' => NULL
  );

  protected $_references = array(
    'area_conhecimento' => array(
      'value' => NULL,
      'class' => 'AreaConhecimento_Model_AreaDataMapper',
      'file'  => 'AreaConhecimento/Model/AreaDataMapper.php'
    ),
    'tipo_base' => array(
      'value' => NULL,
      'class' => 'ComponenteCurricular_Model_TipoBase',
      'file'  => 'ComponenteCurricular/Model/TipoBase.php'
    )
  );

  public function getDataMapper()
  {
    if (is_null($this->_dataMapper)) {
      require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
      $this->setDataMapper(new ComponenteCurricular_Model_ComponenteDataMapper());
    }
    return parent::getDataMapper();
  }

  public function getDefaultValidatorCollection()
  {
    $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());

    $tipoBase = ComponenteCurricular_Model_TipoBase::getInstance();
    $tipos = $tipoBase->getKeys();

    $areas = $this->getDataMapper()->findAreaConhecimento();
    $areas = CoreExt_Entity::entityFilterAttr($areas, 'id');

    return array(
      'instituicao' => new CoreExt_Validate_Choice(array('choices' => $instituicoes)),
      'nome' => new CoreExt_Validate_String(array('min' => 5, 'max' => 200)),
      'abreviatura' => new CoreExt_Validate_String(array('min' => 2, 'max' => 15)),
      'tipo_base' => new CoreExt_Validate_Choice(array('choices' => $tipos)),
      'area_conhecimento' => new CoreExt_Validate_Choice(array('choices' => $areas)),
    );
  }

  /**
   * @see CoreExt_Entity#__toString()
   */
  public function __toString()
  {
    return $this->nome;
  }
}
