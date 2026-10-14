<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);

    $this->params['id'] = $this->registration->ref_cod_aluno;
});

LegacyApiScenario::registerResourceTests('/module/Api/Aluno', [
    'aluno',
    'aluno-search',
    'alunos-matriculados',
    'matriculas',
    'todos-alunos',
    'ocorrencias_disciplinares',
    'grade_ultimo_historico',
    'alunos_by_guardian_cpf',
    'get-nome-bairro',
    'unificacao-alunos',
    'dadosUnificacaoAlunos',
    'dadosMatriculasHistoricosAlunos',
    'deve-habilitar-campo-recursos-prova-inep',
    'deve-obrigar-laudo-medico',
]);
