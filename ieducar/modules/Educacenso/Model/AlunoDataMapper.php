<?php

require_once 'Educacenso/Model/Aluno.php';
require_once 'CoreExt/DataMapper.php';

/**
 * Educacenso_Model_AlunoDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_AlunoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Aluno';
    protected $_tableName   = 'educacenso_cod_aluno';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'aluno'      => 'cod_aluno',
        'alunoInep'  => 'cod_aluno_inep',
        'nomeInep'   => 'nome_inep',
        'fonte'      => 'fonte',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    );

    protected $_primaryKey = array(
        'aluno'      => 'cod_aluno',
        'alunoInep'  => 'cod_aluno_inep'
    );

}
