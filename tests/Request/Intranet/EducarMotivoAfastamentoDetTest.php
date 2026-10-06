<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = WithdrawalReasonFactory::new()->create();
});

test('mostra o detalhe de motivo de afastamento', function () {
    $this->get('/intranet/educar_motivo_afastamento_det.php?cod_motivo_afastamento=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando motivo de afastamento não existe', function () {
    $this->get('/intranet/educar_motivo_afastamento_det.php?cod_motivo_afastamento=0')->assertRedirect();
});
