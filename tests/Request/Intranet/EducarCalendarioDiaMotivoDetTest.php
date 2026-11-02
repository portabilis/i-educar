<?php

use Database\Factories\LegacyCalendarDayReasonFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->reason = LegacyCalendarDayReasonFactory::new()->create(['nm_motivo' => 'Motivo Alfa']);
});

test('mostra o detalhe do motivo de dia do calendário', function () {
    $this->get('/intranet/educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo=' . $this->reason->getKey())
        ->assertOk()
        ->assertSee('Motivo Alfa');
});

test('volta para a listagem quando o motivo não existe', function () {
    $this->get('/intranet/educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo=0')->assertRedirect();
});
