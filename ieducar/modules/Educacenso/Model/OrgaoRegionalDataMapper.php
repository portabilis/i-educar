<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Educacenso/Model/OrgaoRegional.php';

class Educacenso_Model_OrgaoRegionalDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_OrgaoRegional';
    protected $_tableName = 'educacenso_orgao_regional';
    protected $_tableSchema = 'modules';

    protected $_primaryKey = array(
        'sigla_uf' => 'sigla_uf',
        'codigo' => 'codigo',
    );

    protected $_attributeMap = array(
        'sigla_uf' => 'sigla_uf',
        'codigo' => 'codigo',
    );
}
