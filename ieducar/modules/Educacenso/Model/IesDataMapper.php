<?php

class Educacenso_Model_IesDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Ies';
    protected $_tableName   = 'educacenso_ies';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id'                        => 'id',
        'ies'                       => 'ies_id',
        'nome'                      => 'nome',
        'dependenciaAdministrativa' => 'dependencia_administrativa_id',
        'tipoInstituicao'           => 'tipo_instituicao_id',
        'uf'                        => 'uf',
        'user'                      => 'user_id',
        'created_at'                => 'created_at',
        'updated_at'                => 'updated_at'
    ];
}
