<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/Municipio', [
    'municipio-search',
    'municipio-name-search',
]);
