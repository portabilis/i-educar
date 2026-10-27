<?php

use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = CountryFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('lista país', function () {
    $link = 'public_pais_det.php?idpais=' . $this->key;

    $this->get('/intranet/public_pais_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/public_pais_lst.php?nome=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
