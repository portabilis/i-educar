<?php

use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRegimeTypeFactory::new()->create();
});

test('cadastra tipo de regime', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_tipo_regime_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'nm_tipo' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_tipo_regime_cad.php', $payload)->assertRedirect();
});

test('edita e exclui tipo de regime', function () {
    $url = '/intranet/educar_tipo_regime_cad.php?cod_tipo_regime=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_tipo' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
