<?php

use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = DocumentAbsenceReasonFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('mostra o detalhe de motivo de ausência de documentação', function () {
    $this->get('/intranet/educar_motivo_ausencia_documentacao_det.php?id=' . $this->key)->assertOk();
});

test('trata motivo de ausência de documentação inexistente', function () {
    $this->get('/intranet/educar_motivo_ausencia_documentacao_det.php?id=0')->assertRedirect();
});
