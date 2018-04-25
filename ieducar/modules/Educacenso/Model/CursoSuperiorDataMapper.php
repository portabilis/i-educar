<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Educacenso/Model/CursoSuperior.php';

/**
 * Educacenso_Model_CursoSuperiorDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_CursoSuperiorDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_CursoSuperior';
    protected $_tableName   = 'educacenso_curso_superior';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'id'         => 'id',
        'curso'      => 'curso_id',
        'nome'       => 'nome',
        'classe'     => 'classe_id',
        'user'       => 'user_id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    );
}
