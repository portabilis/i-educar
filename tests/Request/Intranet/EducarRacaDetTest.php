<?php

use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRaceFactory::new()->create();
});

test('mostra o detalhe de raça', function () {
    $this->get('/intranet/educar_raca_det.php?cod_raca=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando raça não existe', function () {
    $this->get('/intranet/educar_raca_det.php?cod_raca=0')->assertRedirect();
});
