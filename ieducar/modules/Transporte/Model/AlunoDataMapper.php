<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Transporte/Model/Aluno.php';

/**
 * Transporte_Model_AlunoDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Transporte
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Transporte_Model_AlunoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Transporte_Model_Aluno';
    protected $_tableName   = 'transporte_aluno';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'aluno'       => 'aluno_id',
        'responsavel' => 'responsavel',
        'user'        => 'user_id',
        'created_at'  => 'created_at',
        'updated_at'  => 'updated_at'
    );

    protected $_primaryKey = array(
        'aluno'       => 'aluno_id',
    );

}
