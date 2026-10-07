<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = ReligionFactory::new()->create();
});

test('mostra o detalhe de religião', function () {
    $this->get('/intranet/educar_religiao_det.php?cod_religiao=' . $this->record->getKey())->assertOk();
});

test('responde não encontrado quando religião não existe', function () {
    $this->get('/intranet/educar_religiao_det.php?cod_religiao=0')->assertNotFound();
});
