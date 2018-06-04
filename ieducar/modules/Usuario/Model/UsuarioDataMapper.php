<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Usuario/Model/Usuario.php';

/**
 * Usuario_Model_UsuarioDataMapper class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Usuario_Model_UsuarioDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Usuario_Model_Usuario';
  protected $_tableName   = 'usuario';
  protected $_tableSchema = 'pmieducar';

  protected $_attributeMap = array(
    'id'               => 'cod_usuario',
    'escolaId'         => 'ref_cod_escola',
    'instituicaoId'    => 'ref_cod_instituicao',
    'funcionarioCadId' => 'ref_funcionario_cad',
    'funcionarioExcId' => 'ref_funcionario_exc',
    'tipoUsuarioId'    => 'ref_cod_tipo_usuario',
    'dataCadastro'     => 'data_cadastro',
    'dataExclusao'     => 'data_exclusao',
    'ativo'            => 'ativo'
  );

  protected $_primaryKey = array(
      'id'               => 'cod_usuario'
  );
}
