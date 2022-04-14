<?php

class Avaliacao_Model_NotaComponenteDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_NotaComponente';
    protected $_tableName = 'nota_componente_curricular';
    protected $_tableSchema = 'modules';

    protected $_primaryKey = [
        'id' => 'id',
    ];

    protected $_attributeMap = [
        'id' => 'id',
        'notaAluno' => 'nota_aluno_id',
        'componenteCurricular' => 'componente_curricular_id',
        'nota' => 'nota',
        'notaArredondada' => 'nota_arredondada',
        'etapa' => 'etapa',
        'notaRecuperacaoParalela' => 'nota_recuperacao',
        'notaOriginal' => 'nota_original',
        'notaRecuperacaoEspecifica' => 'nota_recuperacao_especifica'

    ];
}
