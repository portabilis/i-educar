<?php

class Avaliacao_Model_ParecerDescritivoAlunoDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Avaliacao_Model_ParecerDescritivoAluno';
    protected $_tableName   = 'parecer_aluno';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id'                => 'id',
        'matricula'         => 'matricula_id',
        'parecerDescritivo' => 'parecer_descritivo'
    ];
}
