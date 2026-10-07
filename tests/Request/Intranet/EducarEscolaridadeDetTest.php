<?php

use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacySchoolingDegreeFactory::new()->create();
});

test('mostra o detalhe de escolaridade', function () {
    $this->get('/intranet/educar_escolaridade_det.php?idesco=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando escolaridade não existe', function () {
    $this->get('/intranet/educar_escolaridade_det.php?idesco=0')->assertRedirect();
});
