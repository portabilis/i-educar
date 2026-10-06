<?php

use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRegimeTypeFactory::new()->create();
});

test('lista tipo de regime e filtra pelo nome', function () {
    $link = 'educar_tipo_regime_det.php?cod_tipo_regime=' . $this->record->getKey();

    $this->get('/intranet/educar_tipo_regime_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_tipo_regime_lst.php?nm_tipo=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
