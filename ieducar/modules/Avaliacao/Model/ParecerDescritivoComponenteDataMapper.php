<?php

class Avaliacao_Model_ParecerDescritivoComponenteDataMapper extends Avaliacao_Model_ParecerDescritivoAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_ParecerDescritivoComponente';
    protected $_tableName   = 'parecer_componente_curricular';

    protected $_attributeMap = [
        'id'                    => 'id',
        'componenteCurricular'  => 'componente_curricular_id',
        'parecer'               => 'parecer',
        'etapa'                 => 'etapa'
    ];

    protected $_primaryKey = [
        'parecerDescritivoAluno' => 'parecer_aluno_id',
        'componenteCurricular'  => 'componente_curricular_id',
        'etapa' => 'etapa'
    ];
}
