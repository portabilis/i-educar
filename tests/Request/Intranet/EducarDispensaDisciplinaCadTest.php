<?php

use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\Request\Support\LegacyFields;

uses(DiarioApiFakeDataTestTrait::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    // Matrícula enturmada numa turma com um componente curricular vinculado à
    // série, à escola e ao ano letivo.
    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->schoolClass = $enrollment->schoolClass;
    $this->registration = $enrollment->registration;
    $this->createStages($this->schoolClass->school, 1);
    $this->createDisciplines($this->schoolClass, 1);
    $this->discipline = $this->schoolClass->disciplines()->first();

    $this->record = LegacyDisciplineExemptionFactory::new()->create([
        'ref_cod_matricula' => $this->registration,
        'ref_cod_disciplina' => $this->discipline->getKey(),
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
    ]);
    $this->keys = http_build_query([
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_disciplina' => $this->discipline->getKey(),
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
    ]);
});

test('abre o formulário de uma nova dispensa de componente', function () {
    $this->get('/intranet/educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $this->registration->getKey())->assertOk();
});

test('edita a dispensa de componente', function () {
    $url = '/intranet/educar_dispensa_disciplina_cad.php?' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'observacao' => 'Observação alterada',
        'etapa' => [1 => 1],
    ]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->record->refresh()->observacao)->toBe('Observação alterada');
});
