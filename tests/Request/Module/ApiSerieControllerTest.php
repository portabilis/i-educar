<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->schoolClass->ref_ref_cod_serie;
});

LegacyApiScenario::registerResourceTests('/module/Api/Serie', [
    'series',
    'series-curso',
    'series-escola',
    'series-curso-sem-componentes',
    'bloqueio-faixa-etaria',
    'series-curso-grouped',
    'etapa-educacenso',
]);
