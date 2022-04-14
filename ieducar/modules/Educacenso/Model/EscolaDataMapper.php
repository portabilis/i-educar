<?php

class Educacenso_Model_EscolaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Escola';
    protected $_tableName   = 'educacenso_cod_escola';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'escola'        => 'cod_escola',
        'escolaInep'    => 'cod_escola_inep',
        'nomeInep'      => 'nome_inep',
        'fonte'         => 'fonte',
        'created_at'    => 'created_at',
        'updated_at'    => 'updated_at'
    ];

    protected $_primaryKey = [
        'escola'        => 'cod_escola',
        'escolaInep'    => 'cod_escola_inep'
    ];
}
