<?php

use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = CountryFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('cadastra país', function () {
    $payload = LegacyFields::from($this->get('/intranet/public_pais_cad.php')->assertOk()->getContent(), [
        'nome' => 'Registro Alfa',
        'cod_ibge' => 99991,
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/public_pais_cad.php', $payload)->assertRedirect();
});

test('edita e exclui país', function () {
    $url = '/intranet/public_pais_cad.php?idpais=' . $this->key;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nome' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
