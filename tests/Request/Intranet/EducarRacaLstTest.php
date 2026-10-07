<?php

use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRaceFactory::new()->create();
});

test('lista raça e filtra pelo nome', function () {
    $link = 'educar_raca_det.php?cod_raca=' . $this->record->getKey();

    $this->get('/intranet/educar_raca_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_raca_lst.php?nm_raca=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
