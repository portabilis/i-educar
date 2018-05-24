<?php

require_once 'CoreExt/DataMapper.php';
require_once 'AreaConhecimento/Model/Area.php';

/**
 * AreaConhecimento_Model_AreaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     AreaConhecimento
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class AreaConhecimento_Model_AreaDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'AreaConhecimento_Model_Area';
  protected $_tableName   = 'area_conhecimento';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'id'            => 'id',
    'instituicao'   => 'instituicao_id',
    'nome'          => 'nome',
    'secao'         => 'secao',
    'ordenamento_ac'=> 'ordenamento_ac',
  );

  protected $_primaryKey = array(
    'id'          => 'id',
    'instituicao' => 'instituicao_id'
  );
}
