<?php

use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->grade = LegacyGradeFactory::new()->create(['nm_serie' => 'Série Alfa']);
});

test('mostra o detalhe da série', function () {
    $this->get('/intranet/educar_serie_det.php?cod_serie=' . $this->grade->getKey())
        ->assertOk()
        ->assertSee('Série Alfa');
});

test('volta para a listagem quando a série não existe', function () {
    $this->get('/intranet/educar_serie_det.php?cod_serie=0')->assertRedirect();
});
