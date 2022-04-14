<?php

class Educacenso_Model_DocenteDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Docente';
    protected $_tableName   = 'educacenso_cod_docente';
    protected $_tableSchema = 'modules';

    protected $_primaryKey = [
        'docente'       => 'cod_servidor',
        'docenteInep'   => 'cod_docente_inep'
    ];

    protected $_attributeMap = [
        'docente'       => 'cod_servidor',
        'docenteInep'   => 'cod_docente_inep',
        'nomeInep'      => 'nome_inep',
        'fonte'         => 'fonte',
        'created_at'    => 'created_at',
        'updated_at'    => 'updated_at'
    ];
}
