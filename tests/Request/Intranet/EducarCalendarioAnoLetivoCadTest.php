<?php

use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->calendar = LegacyCalendarYearFactory::new()->create();
    $this->day = ['dia' => now()->day, 'mes' => now()->month, 'ano' => $this->calendar->ano, 'ref_cod_calendario_ano_letivo' => $this->calendar->getKey()];
});

test('abre o formulário de um novo calendário', function () {
    $this->get('/intranet/educar_calendario_ano_letivo_cad.php')->assertOk();
});

test('abre o calendário existente e exclui', function () {
    $url = '/intranet/educar_calendario_ano_letivo_cad.php?cod_calendario_ano_letivo=' . $this->calendar->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect();

    expect($this->calendar->refresh()->ativo)->toBe(0);
});
