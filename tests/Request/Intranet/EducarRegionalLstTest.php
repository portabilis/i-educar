<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\RegionalTypeFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = RegionalTypeFactory::new()->create();
});

test('lista regional e filtra pelo nome', function () {
    $link = 'educar_regional_det.php?id=' . $this->record->getKey();

    $this->get('/intranet/educar_regional_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_regional_lst.php?name=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
