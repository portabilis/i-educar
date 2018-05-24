<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Avaliacao/Model/NotaGeral.php';

/**
 * Avaliacao_Model_NotaGeralDataMapper class.
 *
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_NotaGeralDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_NotaGeral';
    protected $_tableName   = 'nota_geral';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'id'                => 'id',
        'notaAluno'         => 'nota_aluno_id',
        'nota'              => 'nota',
        'notaArredondada'   => 'nota_arredondada',
        'etapa'             => 'etapa'
    );

    protected $_primaryKey = array(
        'notaAluno'         => 'nota_aluno_id'
    );
}
