<?php

use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyExemptionTypeFactory::new()->create();
});

test('mostra o detalhe de tipo de dispensa', function () {
    $this->get('/intranet/educar_tipo_dispensa_det.php?cod_tipo_dispensa=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando tipo de dispensa não existe', function () {
    $this->get('/intranet/educar_tipo_dispensa_det.php?cod_tipo_dispensa=0')->assertRedirect();
});
