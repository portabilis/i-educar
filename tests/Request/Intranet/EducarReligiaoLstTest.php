<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = ReligionFactory::new()->create();
});

test('lista religião e filtra pelo nome', function () {
    $link = 'educar_religiao_det.php?cod_religiao=' . $this->record->getKey();

    $this->get('/intranet/educar_religiao_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_religiao_lst.php?nm_religiao=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
