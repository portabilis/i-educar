<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = StateFactory::new()->create();
    $this->key = $this->record->getKey();
});

test('cadastra estado', function () {
    $payload = LegacyFields::from($this->get('/intranet/public_uf_cad.php')->assertOk()->getContent(), [
        'nome' => 'Registro Alfa',
        'idpais' => $this->record->country_id,
        'sigla_uf' => 'ZZ',
        'cod_ibge' => 98,
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/public_uf_cad.php', $payload)->assertRedirect();
});

test('edita e exclui estado', function () {
    $url = '/intranet/public_uf_cad.php?id=' . $this->key;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nome' => 'Registro renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
