<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = StateFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('mostra o detalhe de estado', function () {
    $this->get('/intranet/public_uf_det.php?id=' . $this->key)->assertOk();
});

test('trata estado inexistente', function () {
    $this->get('/intranet/public_uf_det.php?id=0')->assertRedirect();
});
