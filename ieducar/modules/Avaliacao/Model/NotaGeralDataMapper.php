<?php

class Avaliacao_Model_NotaGeralDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_NotaGeral';
    protected $_tableName = 'nota_geral';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id' => 'id',
        'notaAluno' => 'nota_aluno_id',
        'nota' => 'nota',
        'notaArredondada' => 'nota_arredondada',
        'etapa' => 'etapa'
    ];

    protected $_primaryKey = [
        'id' => 'id',
    ];
}
