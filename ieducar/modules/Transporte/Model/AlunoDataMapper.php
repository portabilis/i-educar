<?php

class Transporte_Model_AlunoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Transporte_Model_Aluno';

    protected $_tableName   = 'transporte_aluno';

    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'aluno' => 'aluno_id',
        'responsavel' => 'responsavel',
        'user' => 'user_id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    protected $_primaryKey = [
        'aluno' => 'aluno_id',
    ];
}
