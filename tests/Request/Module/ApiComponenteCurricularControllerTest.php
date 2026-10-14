<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/ComponenteCurricular', [
    'componente_curricular-search',
    'componentecurricular-search',
    'componentes-curriculares',
    'componentes-curriculares-serie',
    'componentes-curriculares-escola-serie-ano',
    'componentes-curriculares-for-multiple-search',
    'componentes-curriculares-migrados',
]);
