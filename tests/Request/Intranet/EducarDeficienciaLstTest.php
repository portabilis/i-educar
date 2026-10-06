<?php

use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyDeficiencyFactory::new()->create();
});

test('lista deficiência e filtra pelo nome', function () {
    $link = 'educar_deficiencia_det.php?cod_deficiencia=' . $this->record->getKey();

    $this->get('/intranet/educar_deficiencia_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_deficiencia_lst.php?nm_deficiencia=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
