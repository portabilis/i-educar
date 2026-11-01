<?php

use Database\Factories\DistrictFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = DistrictFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('lista distrito', function () {
    $link = 'public_distrito_det.php?iddis=' . $this->key;

    $this->get('/intranet/public_distrito_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/public_distrito_lst.php?nome=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
