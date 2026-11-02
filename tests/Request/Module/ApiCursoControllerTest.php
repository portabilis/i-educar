<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->schoolClass->ref_cod_curso;
});

LegacyApiScenario::registerResourceTests('/module/Api/Curso', [
    'cursos',
    'modalidade-curso',
    'cursos-multiple-search',
    'cursos-da-escola',
    'dados-curso',
]);
