<?php

use Database\Factories\CityFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = CityFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('mostra o detalhe de município', function () {
    $this->get('/intranet/public_municipio_det.php?idmun=' . $this->key)->assertOk();
});

test('trata município inexistente', function () {
    $this->get('/intranet/public_municipio_det.php?idmun=0')->assertRedirect();
});
