<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->schoolClass->ref_ref_cod_escola;
});

LegacyApiScenario::registerResourceTests('/module/Api/Escola', [
    'escola',
    'escola-search',
    'etapas-por-escola',
    'etapas-da-escola-por-ano',
    'info-escolas',
    'escolas-multiple-search',
    'escola-ano-letivo',
    'escola-dependencia-administrativa',
    'escolas-usuario',
    'escolas-para-selecao',
    'escolas-para-selecao-sem-filtro-por-usuario',
    'parametros-escolas',
    'endereco-escola',
    'escola-serie-disciplinas-anos-letivos',
]);
