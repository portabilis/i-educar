<?php

class Calendario_Model_TurmaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Calendario_Model_Turma';

    protected $_tableName = 'calendario_turma';

    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'calendarioAnoLetivo' => 'calendario_ano_letivo_id',
        'ano' => 'ano',
        'mes' => 'mes',
        'dia' => 'dia',
        'turma' => 'turma_id'
    ];

    protected $_primaryKey = [
        'calendarioAnoLetivo' => 'calendario_ano_letivo_id',
        'ano' => 'ano',
        'mes' => 'mes',
        'dia' => 'dia',
        'turma' => 'turma_id'
    ];
}
