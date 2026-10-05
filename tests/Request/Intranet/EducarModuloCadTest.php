<?php

use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyStageTypeFactory::new()->create();
});

test('cadastra tipo de etapa', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_modulo_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_tipo' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_modulo_cad.php', $payload)->assertRedirect();
});

test('edita e exclui tipo de etapa', function () {
    $url = '/intranet/educar_modulo_cad.php?cod_modulo=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_tipo' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
