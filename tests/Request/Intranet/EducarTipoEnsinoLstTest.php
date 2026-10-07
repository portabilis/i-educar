<?php

use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyEducationTypeFactory::new()->create();
});

test('lista tipo de ensino e filtra pelo nome', function () {
    $link = 'educar_tipo_ensino_det.php?cod_tipo_ensino=' . $this->record->getKey();

    $this->get('/intranet/educar_tipo_ensino_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_tipo_ensino_lst.php?nm_tipo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
