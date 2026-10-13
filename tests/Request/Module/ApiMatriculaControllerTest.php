<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->registration->getKey();
});

LegacyApiScenario::registerResourceTests('/module/Api/Matricula', [
    'matricula',
    'matriculas',
    'matriculas-escola',
    'frequencia',
    'matricula-search',
    'matricula-transferida',
    'movimentacao-enturmacao',
    'matriculas-dependencia',
    'dispensa-disciplina',
    'enturmacoes-excluidas',
    'busca-ativa',
]);
