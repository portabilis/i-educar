<?php

class Educacenso_Model_CursoSuperiorDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_CursoSuperior';
    protected $_tableName   = 'educacenso_curso_superior';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id'         => 'id',
        'curso'      => 'curso_id',
        'nome'       => 'nome',
        'classe'     => 'classe_id',
        'user'       => 'user_id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];
}
