<?php

use Database\Factories\CityFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = CityFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('cadastra município', function () {
    $payload = LegacyFields::from($this->get('/intranet/public_municipio_cad.php')->assertOk()->getContent(), [
        'nome' => 'Registro Alfa',
        'idpais' => $this->record->state->country_id,
        'iduf' => $this->record->state_id,
        'cod_ibge' => 9999991,
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/public_municipio_cad.php', $payload)->assertRedirect();
});

test('edita e exclui município', function () {
    $url = '/intranet/public_municipio_cad.php?idmun=' . $this->key;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nome' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
