<?php

require_once 'Educacenso/Model/Escola.php';
require_once 'CoreExt/DataMapper.php';

/**
 * Educacenso_Model_EscolaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_EscolaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Escola';
    protected $_tableName   = 'educacenso_cod_escola';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'escola'        => 'cod_escola',
        'escolaInep'    => 'cod_escola_inep',
        'nomeInep'      => 'nome_inep',
        'fonte'         => 'fonte',
        'created_at'    => 'created_at',
        'updated_at'    => 'updated_at'
    );

    protected $_primaryKey = array(
        'escola'        => 'cod_escola',
        'escolaInep'    => 'cod_escola_inep'
    );
}
