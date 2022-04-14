<?php

abstract class Avaliacao_Model_ParecerDescritivoAbstractDataMapper extends CoreExt_DataMapper
{
    protected $_tableSchema = 'modules';

    public function __construct(clsBanco $db = null)
    {
        parent::__construct($db);
        $this->_attributeMap['parecerDescritivoAluno'] = 'parecer_aluno_id';
    }
}
