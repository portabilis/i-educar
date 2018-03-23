<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Configuracao/Model/ConfiguracaoMovimentoGeral.php';

class ConfiguracaoMovimentoGeralDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'ConfiguracaoMovimentoGeral';
    protected $_tableName   = 'config_movimento_geral';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'serie'       => 'ref_cod_serie',
        'coluna'      => 'coluna'
    );
}
