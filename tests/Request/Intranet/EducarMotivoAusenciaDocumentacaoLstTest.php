<?php

use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = DocumentAbsenceReasonFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('lista motivo de ausência de documentação', function () {
    $link = 'educar_motivo_ausencia_documentacao_det.php?id=' . $this->key;

    $this->get('/intranet/educar_motivo_ausencia_documentacao_lst.php')
        ->assertOk()
        ->assertSee($link, false);

    $this->get('/intranet/educar_motivo_ausencia_documentacao_lst.php?name=nome-que-nao-existe')
        ->assertOk()
        ->assertDontSee($link, false);
});
