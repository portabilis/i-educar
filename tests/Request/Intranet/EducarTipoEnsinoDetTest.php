<?php

use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyEducationTypeFactory::new()->create();
});

test('mostra o detalhe de tipo de ensino', function () {
    $this->get('/intranet/educar_tipo_ensino_det.php?cod_tipo_ensino=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando tipo de ensino não existe', function () {
    $this->get('/intranet/educar_tipo_ensino_det.php?cod_tipo_ensino=0')->assertRedirect();
});
