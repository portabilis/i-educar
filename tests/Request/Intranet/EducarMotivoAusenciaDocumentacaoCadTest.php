<?php

use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = DocumentAbsenceReasonFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('cadastra motivo de ausência de documentação', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_motivo_ausencia_documentacao_cad.php')->assertOk()->getContent(), [
        'name' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_motivo_ausencia_documentacao_cad.php', $payload)->assertRedirect();
});

test('edita motivo de ausência de documentação', function () {
    $url = '/intranet/educar_motivo_ausencia_documentacao_cad.php?id=' . $this->key;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['name' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->record->refresh()->name)->toBe('Registro renomeado');
});
