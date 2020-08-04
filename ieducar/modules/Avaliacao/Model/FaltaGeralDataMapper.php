<?php

require_once 'Avaliacao/Model/FaltaAbstractDataMapper.php';
require_once 'Avaliacao/Model/FaltaGeral.php';

class Avaliacao_Model_FaltaGeralDataMapper extends Avaliacao_Model_FaltaAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_FaltaGeral';
    protected $_tableName = 'falta_geral';

    protected $_attributeMap = [
        'id' => 'id',
        'faltaAluno' => 'falta_aluno_id',
        'quantidade' => 'quantidade',
        'etapa' => 'etapa'
    ];

    protected $_primaryKey = [
        'faltaAluno' => 'falta_aluno_id',
        'etapa' => 'etapa'
    ];
}
