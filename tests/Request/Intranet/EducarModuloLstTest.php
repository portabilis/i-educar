<?php

use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyStageTypeFactory::new()->create();
});

test('lista tipo de etapa e filtra pelo nome', function () {
    $link = 'educar_modulo_det.php?cod_modulo=' . $this->record->getKey();

    $this->get('/intranet/educar_modulo_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_modulo_lst.php?nm_tipo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
