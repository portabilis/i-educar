<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/ComponentesSerie', [
    'existe-dispensa',
    'existe-dependencia',
    'get-escolas-by-serie',
]);
