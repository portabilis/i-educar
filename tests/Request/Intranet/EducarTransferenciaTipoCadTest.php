<?php

use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyTransferTypeFactory::new()->create();
});

test('cadastra tipo de transferência', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_transferencia_tipo_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_tipo' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_transferencia_tipo_cad.php', $payload)->assertRedirect();
});

test('edita e exclui tipo de transferência', function () {
    $url = '/intranet/educar_transferencia_tipo_cad.php?cod_transferencia_tipo=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_tipo' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
