<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/Regra', [
    'tabelas-de-arredondamento',
    'regras',
    'regras-recuperacao',
    'regra-serie',
    'regra-serie-ano',
]);
