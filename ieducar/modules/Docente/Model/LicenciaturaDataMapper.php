<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Docente/Model/Licenciatura.php';

/**
 * Docente_Model_LicenciaturaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Docente
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Docente_Model_LicenciaturaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Docente_Model_Licenciatura';
    protected $_tableName   = 'docente_licenciatura';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'id'            => 'id',
        'servidor'      => 'servidor_id',
        'licenciatura'  => 'licenciatura',
        'curso'         => 'curso_id',
        'anoConclusao'  => 'ano_conclusao',
        'ies'           => 'ies_id',
        'user'          => 'user_id',
        'created_at'    => 'created_at',
        'updated_at'    => 'updated_at'
    );
}
