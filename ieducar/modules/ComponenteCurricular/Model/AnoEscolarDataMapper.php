<?php

require_once 'CoreExt/DataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolar.php';

/**
 * ComponenteCurricular_Model_AnoEscolarDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_AnoEscolarDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'ComponenteCurricular_Model_AnoEscolar';
  protected $_tableName   = 'componente_curricular_ano_escolar';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'componenteCurricular'  => 'componente_curricular_id',
    'anoEscolar'            => 'ano_escolar_id',
    'cargaHoraria'          => 'carga_horaria',
    'tipo_nota'             => 'tipo_nota'
  );

  protected $_primaryKey = array(
    'componenteCurricular' => 'componente_curricular_id',
    'anoEscolar'           => 'ano_escolar_id',
  );

  /**
   * @var ComponenteCurricular_Model_ComponenteDataMapper
   */
  protected $_componenteDataMapper = NULL;

  /**
   * Setter.
   * @param ComponenteCurricular_Model_ComponenteDataMapper $mapper
   * @return CoreExt_DataMapper Provê interface fluída
   */
  public function setComponenteDataMapper(ComponenteCurricular_Model_ComponenteDataMapper $mapper)
  {
    $this->_componenteDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return ComponenteCurricular_Model_ComponenteDataMapper
   */
  public function getComponenteDataMapper()
  {
    if (is_null($this->_componenteDataMapper)) {
      require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
      $this->_componenteDataMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
    }
    return $this->_componenteDataMapper;
  }

  /**
   * Finder para componentes por curso.
   *
   * @param int $cursoId
   * @return array
   */
  public function findComponentePorCurso($cursoId)
  {
    $sql = "
SELECT
  DISTINCT(mca.%s)
FROM
  %s mca, pmieducar.serie ps
WHERE
  mca.%s = ps.cod_serie AND ps.ref_cod_curso = '%d'";

    $sql = sprintf($sql, $this->_getTableColumn('componenteCurricular'),
      $this->_getTableName(), $this->_getTableColumn('anoEscolar'), $cursoId);

    $this->getDbAdapter()->Consulta($sql);

    $list = array();
    while ($this->_getDbAdapter()->ProximoRegistro()) {
      $row = $this->_getDbAdapter()->Tupla();
      $list[] = $this->getComponenteDataMapper()->find(
        $row[$this->_getTableColumn('componenteCurricular')]
      );
    }

    return $list;
  }

  /**
   * Finder para componentes por série (ano escolar).
   *
   * @param int $serieId
   * @return array
   */
  public function findComponentePorSerie($serieId)
  {
    $componentesAnoEscolar = $this->findAll(array(), array('anoEscolar' => $serieId));
    $list = array();

    foreach ($componentesAnoEscolar as $key => $componenteAnoEscolar) {
      $id = $componenteAnoEscolar->get('componenteCurricular');
      $list[$id] = $this->getComponenteDataMapper()->find(
        $componenteAnoEscolar->get('componenteCurricular')
      );
      $list[$id]->cargaHoraria = $componenteAnoEscolar->cargaHoraria;
    }

    ksort($list);
    return $list;
  }
}
