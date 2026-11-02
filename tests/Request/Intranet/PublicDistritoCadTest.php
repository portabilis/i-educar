<?php

use Database\Factories\DistrictFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = DistrictFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('cadastra distrito', function () {
    $payload = LegacyFields::from($this->get('/intranet/public_distrito_cad.php')->assertOk()->getContent(), [
        'nome' => 'Registro Alfa',
        'idpais' => $this->record->city->state->country_id,
        'iduf' => $this->record->city->state_id,
        'idmun' => $this->record->city_id,
        'cod_ibge' => 999999991,
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/public_distrito_cad.php', $payload)->assertRedirect();
});

test('edita e exclui distrito', function () {
    $url = '/intranet/public_distrito_cad.php?iddis=' . $this->key;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nome' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
