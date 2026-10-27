<?php

uses(Avaliacao_Service_NotaSituacaoCommon::class);

beforeEach(function () {
    $this->configuraRegra(['tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL]);
});

Avaliacao_Service_NotaSituacaoCommon::registerNotaSituacaoTests();
