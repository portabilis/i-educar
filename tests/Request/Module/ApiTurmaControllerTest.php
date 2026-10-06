<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->schoolClass = LegacySchoolClassFactory::new()->create();
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);

    // Os parâmetros que os recursos da API costumam ler; cada recurso usa os seus.
    $this->params = [
        'access_key' => config('legacy.apis.access_key'),
        'secret_key' => config('legacy.apis.secret_key'),
        'instituicao_id' => $this->schoolClass->ref_cod_instituicao,
        'escola_id' => $this->schoolClass->ref_ref_cod_escola,
        'curso_id' => $this->schoolClass->ref_cod_curso,
        'serie_id' => $this->schoolClass->ref_ref_cod_serie,
        'turma_id' => $this->schoolClass->getKey(),
        'ano' => $this->schoolClass->ano,
        'ano_letivo' => $this->schoolClass->ano,
        'matricula_id' => $this->registration->getKey(),
        'aluno_id' => $this->registration->ref_cod_aluno,
        'query' => 'a',
    ];

    $this->params['id'] = $this->schoolClass->getKey();
});

test('responde ao recurso', function (string $resource) {
    $this->get('/module/Api/Turma?' . http_build_query(['oper' => 'get', 'resource' => $resource] + $this->params))
        ->assertOk()
        ->assertJsonPath('resource', $resource);
})->with([
    'turma',
    'ordena-matriculas-data-base',
    'ordena-turma-alfabetica',
    'turmas-por-escola',
    'alunos-exame-turma',
    'series-da-turma',
]);
