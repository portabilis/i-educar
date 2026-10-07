<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->individual = LegacyIndividualFactory::new()->create();
});

test('mostra o detalhe da pessoa física', function () {
    $this->get('/intranet/atendidos_det.php?cod_pessoa=' . $this->individual->getKey())->assertOk();
});
