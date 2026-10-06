<?php

use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->organization = LegacyOrganizationFactory::new()->create(['fantasia' => 'Empresa Alfa']);
});

test('abre o formulário de uma nova pessoa jurídica', function () {
    $this->get('/intranet/empresas_cad.php')->assertOk();
});

test('edita a pessoa jurídica', function () {
    $url = '/intranet/empresas_cad.php?idpes=' . $this->organization->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['fantasia' => 'Empresa renomeada']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->organization->refresh()->fantasia)->toBe('Empresa renomeada');
});
