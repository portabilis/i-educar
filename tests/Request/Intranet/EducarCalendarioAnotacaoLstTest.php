<?php

use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyCalendarDayNoteFactory;
use Database\Factories\LegacyCalendarNoteFactory;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->calendar = LegacyCalendarYearFactory::new()->create();
    $this->day = ['dia' => now()->day, 'mes' => now()->month, 'ano' => $this->calendar->ano, 'ref_cod_calendario_ano_letivo' => $this->calendar->getKey()];
    LegacyCalendarDayFactory::new()->create(['ref_cod_calendario_ano_letivo' => $this->calendar]);

    $this->note = LegacyCalendarNoteFactory::new()->create(['nm_anotacao' => 'Anotação Alfa']);
    LegacyCalendarDayNoteFactory::new()->create([
        'ref_dia' => $this->day['dia'],
        'ref_mes' => $this->day['mes'],
        'ref_ref_cod_calendario_ano_letivo' => $this->calendar->getKey(),
        'ref_cod_calendario_anotacao' => $this->note,
    ]);
});

test('lista as anotações do dia do calendário', function () {
    $this->get('/intranet/educar_calendario_anotacao_lst.php?' . http_build_query($this->day))
        ->assertOk()
        ->assertSee('Anotação Alfa');
});
