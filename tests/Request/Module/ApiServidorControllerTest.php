<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/Servidor', [
    'servidor-search',
    'escolaridade',
    'servidores-disciplinas-turmas',
    'dados-servidor',
    'verifica-deficiencias',
    'servidores',
    'unificacoes',
]);
