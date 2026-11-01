<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = StateFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('lista estado', function () {
    $link = 'public_uf_det.php?id=' . $this->key;

    $this->get('/intranet/public_uf_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/public_uf_lst.php?nome=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
