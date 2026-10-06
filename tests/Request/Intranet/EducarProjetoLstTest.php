<?php

use Database\Factories\LegacyProjectFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyProjectFactory::new()->create();
});

test('lista projeto e filtra pelo nome', function () {
    $link = 'educar_projeto_det.php?cod_projeto=' . $this->record->getKey();

    $this->get('/intranet/educar_projeto_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_projeto_lst.php?nome=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
