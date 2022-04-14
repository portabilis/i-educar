<?php

class ConfiguracaoMovimentoGeralDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'ConfiguracaoMovimentoGeral';
    protected $_tableName   = 'config_movimento_geral';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'serie'       => 'ref_cod_serie',
        'coluna'      => 'coluna'
    ];
}
