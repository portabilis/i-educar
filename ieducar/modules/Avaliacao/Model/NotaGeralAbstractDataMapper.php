<?php

abstract class Avaliacao_Model_NotaGeralAbstractDataMapper extends CoreExt_DataMapper
{
    protected $_tableSchema = 'modules';

    public function __construct(clsBanco $db = null)
    {
        parent::__construct($db);
        $this->_attributeMap['notaAluno'] = 'nota_aluno_id';
        $this->_attributeMap['notaArredondada'] = 'nota_arredondada';
    }
}
