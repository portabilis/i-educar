<?php

use Database\Factories\CityFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = CityFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('lista município', function () {
    $link = 'public_municipio_det.php?idmun=' . $this->key;

    $this->get('/intranet/public_municipio_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/public_municipio_lst.php?nome=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
