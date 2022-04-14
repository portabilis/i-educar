<?php

class Avaliacao_Model_FaltaComponenteDataMapper extends Avaliacao_Model_FaltaAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_FaltaComponente';
    protected $_tableName = 'falta_componente_curricular';

    protected $_attributeMap = [
        'faltaAluno' => 'falta_aluno_id',
        'componenteCurricular' => 'componente_curricular_id',
        'quantidade' => 'quantidade',
        'etapa' => 'etapa'
    ];

    protected $_primaryKey = [
        'id' => 'id',
    ];
}
