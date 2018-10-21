<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Docente/Model/Licenciatura.php';

class Docente_Model_LicenciaturaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Docente_Model_Licenciatura';
    protected $_tableName   = 'docente_licenciatura';
    protected $_tableSchema = 'modules';
    protected $_attributeMap = [
        'id'           => 'id',
        'servidor'     => 'servidor_id',
        'licenciatura' => 'licenciatura',
        'curso'        => 'curso_id',
        'anoConclusao' => 'ano_conclusao',
        'ies'          => 'ies_id',
        'user'         => 'user_id',
        'created_at'   => 'created_at',
        'updated_at'   => 'updated_at'
    ];
}
