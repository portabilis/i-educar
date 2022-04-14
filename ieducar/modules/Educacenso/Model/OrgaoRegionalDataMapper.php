<?php

class Educacenso_Model_OrgaoRegionalDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_OrgaoRegional';
    protected $_tableName = 'educacenso_orgao_regional';
    protected $_tableSchema = 'modules';

    protected $_primaryKey = [
        'sigla_uf' => 'sigla_uf',
        'codigo' => 'codigo',
    ];

    protected $_attributeMap = [
        'sigla_uf' => 'sigla_uf',
        'codigo' => 'codigo',
    ];
}
