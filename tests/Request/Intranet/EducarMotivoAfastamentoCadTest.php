<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = WithdrawalReasonFactory::new()->create();
});

test('cadastra motivo de afastamento', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_motivo_afastamento_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_motivo' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_motivo_afastamento_cad.php', $payload)->assertRedirect();
});

test('edita e exclui motivo de afastamento', function () {
    $url = '/intranet/educar_motivo_afastamento_cad.php?cod_motivo_afastamento=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_motivo' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
