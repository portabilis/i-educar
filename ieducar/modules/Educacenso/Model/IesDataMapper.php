<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Educacenso/Model/Ies.php';

/**
 * Educacenso_Model_IesDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_IesDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Ies';
    protected $_tableName   = 'educacenso_ies';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'id'                        => 'id',
        'ies'                       => 'ies_id',
        'nome'                      => 'nome',
        'dependenciaAdministrativa' => 'dependencia_administrativa_id',
        'tipoInstituicao'           => 'tipo_instituicao_id',
        'uf'                        => 'uf',
        'user'                      => 'user_id',
        'created_at'                => 'created_at',
        'updated_at'                => 'updated_at'
    );
}
