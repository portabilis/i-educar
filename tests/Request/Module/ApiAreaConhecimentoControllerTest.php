<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/AreaConhecimento', [
    'areas-de-conhecimento',
    'areaconhecimento-serie',
    'areaconhecimento-turma',
    'areaconhecimento-escolaserie',
]);
