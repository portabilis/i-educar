<?php

use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyStageTypeFactory::new()->create();
});

test('mostra o detalhe de tipo de etapa', function () {
    $this->get('/intranet/educar_modulo_det.php?cod_modulo=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando tipo de etapa não existe', function () {
    $this->get('/intranet/educar_modulo_det.php?cod_modulo=0')->assertRedirect();
});
