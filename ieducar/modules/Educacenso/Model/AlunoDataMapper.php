<?php

class Educacenso_Model_AlunoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Educacenso_Model_Aluno';
    protected $_tableName   = 'educacenso_cod_aluno';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'aluno'      => 'cod_aluno',
        'alunoInep'  => 'cod_aluno_inep',
        'nomeInep'   => 'nome_inep',
        'fonte'      => 'fonte',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    protected $_primaryKey = [
        'aluno'      => 'cod_aluno',
        'alunoInep'  => 'cod_aluno_inep'
    ];
}
