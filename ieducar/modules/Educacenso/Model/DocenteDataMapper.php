<?php

require_once 'Educacenso/Model/Docente.php';
require_once 'CoreExt/DataMapper.php';

/**
 * Educacenso_Model_DocenteDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_DocenteDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Docente';
    protected $_tableName   = 'educacenso_cod_docente';
    protected $_tableSchema = 'modules';

    protected $_primaryKey = array(
        'docente'       => 'cod_servidor',
        'docenteInep'   => 'cod_docente_inep'
    );

    protected $_attributeMap = array(
        'docente'       => 'cod_servidor',
        'docenteInep'   => 'cod_docente_inep',
        'nomeInep'      => 'nome_inep',
        'fonte'         => 'fonte',
        'created_at'    => 'created_at',
        'updated_at'    => 'updated_at'
    );

}
