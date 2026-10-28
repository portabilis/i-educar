<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/DynamicInput/Etapa', [
    'etapas',
    'etapasEscola',
]);
