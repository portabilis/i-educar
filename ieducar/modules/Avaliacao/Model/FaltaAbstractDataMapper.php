<?php

abstract class Avaliacao_Model_FaltaAbstractDataMapper extends CoreExt_DataMapper
{
    protected $_tableSchema = 'modules';

    public function __construct(clsBanco $db = null)
    {
        parent::__construct($db);
        $this->_attributeMap['faltaAluno'] = 'falta_aluno_id';
    }
}
