<?php

use Database\Factories\DistrictFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = DistrictFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('mostra o detalhe de distrito', function () {
    $this->get('/intranet/public_distrito_det.php?iddis=' . $this->key)->assertOk();
});

test('trata distrito inexistente', function () {
    $this->get('/intranet/public_distrito_det.php?iddis=0')->assertRedirect();
});
