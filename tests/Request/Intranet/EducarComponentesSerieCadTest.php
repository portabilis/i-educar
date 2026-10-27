<?php

use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->grade = LegacyGradeFactory::new()->create(['nm_serie' => 'Série Alfa']);
});

test('abre a configuração dos componentes da série no modo editar com seletor simples', function () {
    $this->get('/intranet/educar_componentes_serie_cad.php?serie_id=' . $this->grade->getKey())
        ->assertOk()
        ->assertDontSee("multiple='multiple'", false);
});

test('abre o cadastro de componentes da série no modo novo com seletor múltiplo', function () {
    $this->get('/intranet/educar_componentes_serie_cad.php')
        ->assertOk()
        ->assertSee("multiple='multiple'", false);
});
