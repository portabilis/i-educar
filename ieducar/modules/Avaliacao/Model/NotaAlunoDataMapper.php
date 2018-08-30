<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Avaliacao/Model/NotaAluno.php';

class Avaliacao_Model_NotaAlunoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_NotaAluno';
    protected $_tableName = 'nota_aluno';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id' => 'id',
        'matricula' => 'matricula_id'
    ];
}
