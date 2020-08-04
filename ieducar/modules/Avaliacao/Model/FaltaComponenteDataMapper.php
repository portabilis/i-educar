<?php

require_once 'Avaliacao/Model/FaltaAbstractDataMapper.php';
require_once 'Avaliacao/Model/FaltaComponente.php';

class Avaliacao_Model_FaltaComponenteDataMapper extends Avaliacao_Model_FaltaAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_FaltaComponente';
    protected $_tableName = 'falta_componente_curricular';

    protected $_attributeMap = [
        'id' => 'id',
        'faltaAluno' => 'falta_aluno_id',
        'componenteCurricular' => 'componente_curricular_id',
        'quantidade' => 'quantidade',
        'etapa' => 'etapa'
    ];

    protected $_primaryKey = [
        'faltaAluno' => 'falta_aluno_id',
        'componenteCurricular' => 'componente_curricular_id',
        'etapa' => 'etapa'
    ];
}
