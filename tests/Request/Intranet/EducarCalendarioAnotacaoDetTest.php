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

test('mostra o detalhe da anotação do calendário', function () {
    $this->get('/intranet/educar_calendario_anotacao_det.php?cod_calendario_anotacao=' . $this->note->getKey() . '&' . http_build_query($this->day))
        ->assertOk()
        ->assertSee('Anotação Alfa');
});

test('volta para o calendário avisando quando a anotação não existe', function () {
    $this->get('/intranet/educar_calendario_anotacao_det.php?cod_calendario_anotacao=0')
        ->assertRedirect('educar_calendario_ano_letivo_lst.php')
        ->assertSessionHas('error', 'Anotação do calendário não encontrada. O registro não existe ou foi excluído. Acesse a anotação pelo calendário do ano letivo.');
});
