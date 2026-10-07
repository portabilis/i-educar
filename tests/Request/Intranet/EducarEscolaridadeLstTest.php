<?php

use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacySchoolingDegreeFactory::new()->create();
});

test('lista escolaridade e filtra pelo nome', function () {
    $link = 'educar_escolaridade_det.php?idesco=' . $this->record->getKey();

    $this->get('/intranet/educar_escolaridade_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_escolaridade_lst.php?descricao=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
