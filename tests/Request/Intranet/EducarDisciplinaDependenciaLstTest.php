<?php

use Database\Factories\LegacyDisciplineDependenceFactory;
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

    $this->record = LegacyDisciplineDependenceFactory::new()->create([
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

test('lista as dependências da matrícula', function () {
    $this->get('/intranet/educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->registration->getKey())
        ->assertOk()
        ->assertSee('educar_disciplina_dependencia_det.php');
});

test('volta para a listagem de alunos quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_disciplina_dependencia_lst.php')->assertRedirect();
});
