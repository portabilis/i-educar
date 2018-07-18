<?php

require_once 'CoreExt/DataMapper.php';
require_once 'RegraAvaliacao/Model/SerieAno.php';

class RegraAvaliacao_Model_SerieAnoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'RegraAvaliacao_Model_SerieAno';
    protected $_tableName = 'regra_avaliacao_serie_ano';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'regraAvaliacao' => 'regra_avaliacao_id',
        'regraAvaliacaoDiferenciada' => 'regra_avaliacao_diferenciada_id',
        'serie' => 'serie_id',
        'anoLetivo' => 'ano_letivo',
    );

    protected $_primaryKey = [
        'serie' => 'serie_id',
        'anoLetivo' => 'ano_letivo',
    ];
}
