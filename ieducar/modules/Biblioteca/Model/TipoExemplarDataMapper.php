<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Biblioteca/Model/TipoExemplar.php';

/**
 * Usuario_Model_TipoExemplarDataMapper class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Biblioteca_Model_TipoExemplarDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Biblioteca_Model_TipoExemplar';
  protected $_tableName   = 'exemplar_tipo';
  protected $_tableSchema = 'pmieducar';


  protected $_attributeMap = array(
    'cod_exemplar_tipo'  => 'cod_exemplar_tipo',
    'ref_cod_biblioteca' => 'ref_cod_biblioteca',
    'ref_usuario_exc'    => 'ref_usuario_exc',
    'ref_usuario_cad'    => 'ref_usuario_cad',
    'nm_tipo'            => 'nm_tipo',
    'descricao'          => 'descricao',
    'data_cadastro'      => 'data_cadastro',
    'data_exclusao'      => 'data_exclusao',
    'ativo'              => 'ativo'
  );


  protected $_notPersistable = array();


  protected $_primaryKey = array(
    'cod_exemplar_tipo' => 'cod_exemplar_tipo'
  );

}
