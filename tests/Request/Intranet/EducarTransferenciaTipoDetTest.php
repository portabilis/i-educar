<?php

use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyTransferTypeFactory::new()->create();
});

test('mostra o detalhe de tipo de transferência', function () {
    $this->get('/intranet/educar_transferencia_tipo_det.php?cod_transferencia_tipo=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando tipo de transferência não existe', function () {
    $this->get('/intranet/educar_transferencia_tipo_det.php?cod_transferencia_tipo=0')->assertRedirect();
});
