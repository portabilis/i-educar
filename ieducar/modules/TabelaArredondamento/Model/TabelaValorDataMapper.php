<?php

require_once 'CoreExt/DataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'App/Model/IedFinder.php';

class TabelaArredondamento_Model_TabelaValorDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'TabelaArredondamento_Model_TabelaValor';
    protected $_tableName = 'tabela_arredondamento_valor';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'tabelaArredondamento' => 'tabela_arredondamento_id',
        'valorMinimo' => 'valor_minimo',
        'valorMaximo' => 'valor_maximo',
        'acao' => 'acao',
        'casaDecimalExata' => 'casa_decimal_exata'
    ];
}
