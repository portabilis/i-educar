<?php

require_once 'CoreExt/DataMapper.php';
require_once 'RegraAvaliacao/Model/RegraRecuperacao.php';
require_once 'App/Model/IedFinder.php';

class RegraAvaliacao_Model_RegraRecuperacaoDataMapper extends CoreExt_DataMapper
{

    protected $_entityClass = 'RegraAvaliacao_Model_RegraRecuperacao';
    protected $_tableName = 'regra_avaliacao_recuperacao';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'regraAvaliacao' => 'regra_avaliacao_id',
        'etapasRecuperadas' => 'etapas_recuperadas',
        'substituiMenorNota' => 'substitui_menor_nota',
        'notaMaxima' => 'nota_maxima'
    ];
}
