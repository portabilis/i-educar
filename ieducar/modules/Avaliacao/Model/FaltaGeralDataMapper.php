<?php

require_once 'Avaliacao/Model/FaltaAbstractDataMapper.php';
require_once 'Avaliacao/Model/FaltaGeral.php';

/**
 * Avaliacao_Model_FaltaGeralDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_FaltaGeralDataMapper extends Avaliacao_Model_FaltaAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_FaltaGeral';
    protected $_tableName   = 'falta_geral';

    protected $_attributeMap = array(
        'id'            => 'id',
        'faltaAluno'    => 'falta_aluno_id',
        'quantidade'    => 'quantidade',
        'etapa'         => 'etapa'
    );

    protected $_primaryKey = array(
        'faltaAluno'    => 'falta_aluno_id',
        'etapa'         => 'etapa'
    );
}
