<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Avaliacao/Model/MediaGeral.php';

/**
 * Avaliacao_Model_MediaGeralDataMapper class.
 *
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_MediaGeralDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_MediaGeral';
    protected $_tableName   = 'media_geral';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'notaAluno'         => 'nota_aluno_id',
        'media'             => 'media',
        'mediaArredondada'  => 'media_arredondada',
        'etapa'             => 'etapa'
    );

    protected $_primaryKey = array(
        'notaAluno'   => 'nota_aluno_id',
        'etapa'       => 'etapa'
    );
}
