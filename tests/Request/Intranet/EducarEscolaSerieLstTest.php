<?php

use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolGrade = LegacySchoolGradeFactory::new()->create();
    $this->keys = 'ref_cod_escola=' . $this->schoolGrade->ref_cod_escola . '&ref_cod_serie=' . $this->schoolGrade->ref_cod_serie;
});

test('lista as séries da escola', function () {
    $this->get('/intranet/educar_escola_serie_lst.php?busca=S&ref_cod_escola=' . $this->schoolGrade->ref_cod_escola)
        ->assertOk()
        ->assertSee('educar_escola_serie_det.php?' . $this->keys, false);
});
