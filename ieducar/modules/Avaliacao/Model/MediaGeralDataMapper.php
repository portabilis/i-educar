<?php

class Avaliacao_Model_MediaGeralDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_MediaGeral';
    protected $_tableName   = 'media_geral';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'notaAluno'         => 'nota_aluno_id',
        'media'             => 'media',
        'mediaArredondada'  => 'media_arredondada',
        'etapa'             => 'etapa'
    ];

    protected $_primaryKey = [
        'notaAluno'   => 'nota_aluno_id',
        'etapa'       => 'etapa'
    ];
}
