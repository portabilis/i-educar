<?php

use Database\Factories\LegacyCalendarDayReasonFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->reason = LegacyCalendarDayReasonFactory::new()->create(['nm_motivo' => 'Motivo Alfa']);
});

test('cadastra um motivo de dia do calendário', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_calendario_dia_motivo_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_escola' => $this->reason->ref_cod_escola,
        'nm_motivo' => 'Motivo Beta',
        'sigla' => 'MB',
        'tipo' => 'e',
    ]);

    $this->post('/intranet/educar_calendario_dia_motivo_cad.php', $payload)->assertRedirect();

    $this->assertDatabaseHas('pmieducar.calendario_dia_motivo', ['nm_motivo' => 'Motivo Beta']);
});

test('edita e exclui o motivo de dia do calendário', function () {
    $url = '/intranet/educar_calendario_dia_motivo_cad.php?cod_calendario_dia_motivo=' . $this->reason->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_motivo' => 'Motivo renomeado']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->reason->refresh()->nm_motivo)->toBe('Motivo renomeado');

    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
