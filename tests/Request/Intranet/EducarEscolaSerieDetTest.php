<?php

use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolGrade = LegacySchoolGradeFactory::new()->create();
    $this->keys = 'ref_cod_escola=' . $this->schoolGrade->ref_cod_escola . '&ref_cod_serie=' . $this->schoolGrade->ref_cod_serie;
});

test('mostra o detalhe da série da escola', function () {
    $this->get('/intranet/educar_escola_serie_det.php?' . $this->keys)->assertOk();
});

test('volta para a listagem quando a série da escola não existe', function () {
    $this->get('/intranet/educar_escola_serie_det.php?ref_cod_escola=0&ref_cod_serie=0')->assertRedirect();
});
