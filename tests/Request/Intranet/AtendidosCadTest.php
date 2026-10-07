<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->individual = LegacyIndividualFactory::new()->create();
});

test('abre o formulário de uma nova pessoa física', function () {
    $this->get('/intranet/atendidos_cad.php')->assertOk();
});

test('abre o formulário de edição da pessoa física', function () {
    $payload = LegacyFields::from($this->get('/intranet/atendidos_cad.php?cod_pessoa_fj=' . $this->individual->getKey())->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar');
});
