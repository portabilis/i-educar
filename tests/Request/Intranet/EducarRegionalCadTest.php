<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\RegionalTypeFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = RegionalTypeFactory::new()->create();
});

test('cadastra regional', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_regional_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'name' => 'Registro Alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_regional_cad.php', $payload)->assertRedirect();
});

test('edita e exclui regional', function () {
    $url = '/intranet/educar_regional_cad.php?id=' . $this->record->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['name' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
