<?php

use Database\Factories\LegacyUserFactory;
use Tests\Api\DiarioApiFakeDataTestTrait;

uses(DiarioApiFakeDataTestTrait::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    // Matrícula enturmada, com etapa e um componente curricular lançáveis.
    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->schoolClass = $enrollment->schoolClass;
    $this->registration = $enrollment->registration;
    $this->createStages($this->schoolClass->school, 1);
    $this->createDisciplines($this->schoolClass, 1);
    $this->params = [
        'instituicao_id' => $this->schoolClass->ref_cod_instituicao,
        'escola_id' => $this->schoolClass->ref_ref_cod_escola,
        'curso_id' => $this->schoolClass->ref_cod_curso,
        'serie_id' => $this->schoolClass->ref_ref_cod_serie,
        'turma_id' => $this->schoolClass->getKey(),
        'ano' => $this->schoolClass->ano,
    ];
});

test('aceita lançamentos vazios em cada recurso do diário', function (string $resource) {
    $this->post('/module/Api/Diario?' . http_build_query([
        'oper' => 'post',
        'resource' => $resource,
        'access_key' => config('legacy.apis.access_key'),
        'secret_key' => config('legacy.apis.secret_key'),
        'instituicao_id' => $this->schoolClass->ref_cod_instituicao,
        'etapa' => 1,
    ]), ['notas' => [], 'faltas' => [], 'pareceres' => []])
        ->assertOk()
        ->assertJsonPath('resource', $resource);
})->with([
    'notas',
    'recuperacoes',
    'faltas-por-componente',
    'faltas-geral',
    'pareceres-por-etapa-e-componente',
    'pareceres-por-etapa-geral',
    'pareceres-anual-por-componente',
    'pareceres-anual-geral',
]);
