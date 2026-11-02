<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/DynamicInput/AnoLetivo', [
    'anos_letivos',
    'anos_letivos_escola_serie',
    'anos_letivos_em_comum_series_da_escola',
]);
