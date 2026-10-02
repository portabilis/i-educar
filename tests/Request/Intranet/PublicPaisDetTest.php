<?php

use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = CountryFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('mostra o detalhe de país', function () {
    $this->get('/intranet/public_pais_det.php?idpais=' . $this->key)->assertOk();
});

test('trata país inexistente', function () {
    $this->get('/intranet/public_pais_det.php?idpais=0')->assertRedirect();
});
