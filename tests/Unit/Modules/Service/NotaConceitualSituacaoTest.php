<?php

class Avaliacao_Service_NotaConceitualSituacaoTest extends Avaliacao_Service_NotaSituacaoCommon
{
    protected function setUp(): void
    {
        $this->_setRegraOption('tipoNota', RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL);
        parent::setUp();
    }
}
