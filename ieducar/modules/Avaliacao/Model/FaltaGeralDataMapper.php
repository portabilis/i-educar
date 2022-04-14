<?php

class Avaliacao_Model_FaltaGeralDataMapper extends Avaliacao_Model_FaltaAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_FaltaGeral';
    protected $_tableName = 'falta_geral';

    protected $_attributeMap = [
        'faltaAluno' => 'falta_aluno_id',
        'quantidade' => 'quantidade',
        'etapa' => 'etapa'
    ];

    protected $_primaryKey = [
        'id' => 'id',
    ];
}
