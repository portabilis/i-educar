<?php

use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolGrade = LegacySchoolGradeFactory::new()->create();
    $this->keys = 'ref_cod_escola=' . $this->schoolGrade->ref_cod_escola . '&ref_cod_serie=' . $this->schoolGrade->ref_cod_serie;
});

test('abre o formulário de uma nova série da escola', function () {
    $this->get('/intranet/educar_escola_serie_cad.php')->assertOk();
});

test('abre o formulário de edição da série da escola', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_escola_serie_cad.php?' . $this->keys)->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar');
});
