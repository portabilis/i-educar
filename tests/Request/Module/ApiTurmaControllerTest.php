<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->schoolClass->getKey();
});

LegacyApiScenario::registerResourceTests('/module/Api/Turma', [
    'turma',
    'ordena-matriculas-data-base',
    'ordena-turma-alfabetica',
    'turmas-por-escola',
    'alunos-exame-turma',
    'series-da-turma',
]);
