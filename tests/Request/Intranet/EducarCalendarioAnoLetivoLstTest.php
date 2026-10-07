<?php

use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->calendar = LegacyCalendarYearFactory::new()->create();
    $this->day = ['dia' => now()->day, 'mes' => now()->month, 'ano' => $this->calendar->ano, 'ref_cod_calendario_ano_letivo' => $this->calendar->getKey()];
});

test('mostra o calendário do ano letivo da escola', function () {
    LegacyCalendarDayFactory::new()->create(['ref_cod_calendario_ano_letivo' => $this->calendar]);

    $this->post('/intranet/educar_calendario_ano_letivo_lst.php', [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_escola' => $this->calendar->ref_cod_escola,
        'ano' => $this->calendar->ano,
        'mes' => now()->month,
        'cod_calendario_ano_letivo' => $this->calendar->getKey(),
    ])->assertOk();
});

test('abre a tela de calendário sem filtro', function () {
    $this->get('/intranet/educar_calendario_ano_letivo_lst.php')->assertOk();
});
