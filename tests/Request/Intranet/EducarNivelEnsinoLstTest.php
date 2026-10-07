<?php

use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyEducationLevelFactory::new()->create();
});

test('lista nível de ensino e filtra pelo nome', function () {
    $link = 'educar_nivel_ensino_det.php?cod_nivel_ensino=' . $this->record->getKey();

    $this->get('/intranet/educar_nivel_ensino_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_nivel_ensino_lst.php?nm_nivel=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
