<?php

use Database\Factories\LegacyCalendarDayReasonFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->reason = LegacyCalendarDayReasonFactory::new()->create(['nm_motivo' => 'Motivo Alfa']);
});

test('lista os motivos de dia do calendário', function () {
    $this->get('/intranet/educar_calendario_dia_motivo_lst.php?busca=S&ref_cod_instituicao=' . $this->user->ref_cod_instituicao . '&ref_cod_escola=' . $this->reason->ref_cod_escola)
        ->assertOk()
        ->assertSee('Motivo Alfa');
});
