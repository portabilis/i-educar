<?php

use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Api\DiarioApiFakeDataTestTrait;

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

test('mostra o detalhe da dispensa de componente', function () {
    $this->get('/intranet/educar_dispensa_disciplina_det.php?' . $this->keys)->assertOk();
});

test('volta para a listagem quando a dispensa de componente não existe', function () {
    $this->get('/intranet/educar_dispensa_disciplina_det.php?ref_cod_matricula=' . $this->registration->getKey() . '&ref_cod_disciplina=0&ref_cod_escola=0&ref_cod_serie=0')
        ->assertRedirect();
});
