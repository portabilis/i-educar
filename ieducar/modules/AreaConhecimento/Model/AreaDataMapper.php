<?php

class AreaConhecimento_Model_AreaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'AreaConhecimento_Model_Area';

    protected $_tableName   = 'area_conhecimento';

    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id' => 'id',
        'instituicao' => 'instituicao_id',
        'nome' => 'nome',
        'secao' => 'secao',
        'ordenamento_ac' => 'ordenamento_ac',
        'agrupar_descritores' => 'agrupar_descritores',
    ];

    protected $_primaryKey = [
        'id' => 'id',
        'instituicao' => 'instituicao_id'
    ];
}
