<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->user->getKey();
});

LegacyApiScenario::registerResourceTests('/module/Api/Pessoa', [
    'pessoa-search',
    'pessoa',
    'info-servidor',
    'exist-servidor',
    'pessoa-parent',
    'reativarPessoa',
    'dadosUnificacaoPessoa',
]);
