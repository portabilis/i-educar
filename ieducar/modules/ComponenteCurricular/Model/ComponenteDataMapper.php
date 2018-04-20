<?php

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

  protected $_primaryKey = array(
      'id'          => 'id',
      'instituicao' => 'instituicao_id'
  );

  protected $_attributeMap = array(
      'id'                  => 'id',
      'instituicao'         => 'instituicao_id',
      'area_conhecimento'   => 'area_conhecimento_id',
      'nome'                => 'nome',
      'abreviatura'         => 'abreviatura',
      'tipo_base'           => 'tipo_base',
      'codigo_educacenso'   => 'codigo_educacenso',
      'ordenamento'         => 'ordenamento'

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
    $anoEscolar = $this->getAnoEscolarDataMapper()->find(array(
        'componenteCurricular' => $componenteCurricular,
        'anoEscolar'           =>  $anoEscolar)
    );
    $componenteCurricular = $this->find(array(
        'id'          => $componenteCurricular,
//        'instituicao' => 1
    ));
    $componenteCurricular->cargaHoraria = $anoEscolar->cargaHoraria;
    return $componenteCurricular;
  }
}
