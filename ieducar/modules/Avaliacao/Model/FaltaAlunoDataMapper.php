<?php

class Avaliacao_Model_FaltaAlunoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_FaltaAluno';
    protected $_tableName   = 'falta_aluno';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id'        => 'id',
        'matricula' => 'matricula_id',
        'tipoFalta' => 'tipo_falta'
    ];
}
