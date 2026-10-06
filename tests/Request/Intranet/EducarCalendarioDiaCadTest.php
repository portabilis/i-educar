<?php

use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyCalendarDayReasonFactory;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->calendar = LegacyCalendarYearFactory::new()->create();
    $this->day = ['dia' => now()->day, 'mes' => now()->month, 'ano' => $this->calendar->ano, 'ref_cod_calendario_ano_letivo' => $this->calendar->getKey()];
});

test('define o motivo de um dia do calendário', function () {
    $reason = LegacyCalendarDayReasonFactory::new()->create(['ref_cod_escola' => $this->calendar->ref_cod_escola]);
    $url = '/intranet/educar_calendario_dia_cad.php?' . http_build_query($this->day);

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'ref_cod_calendario_dia_motivo' => $reason->getKey(),
        'descricao' => 'Dia de conselho',
    ]);

    $this->post($url, $payload)->assertRedirect();
});

test('abre e exclui um dia já definido no calendário', function () {
    LegacyCalendarDayFactory::new()->create(['ref_cod_calendario_ano_letivo' => $this->calendar]);
    $url = '/intranet/educar_calendario_dia_cad.php?' . http_build_query($this->day);

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect();
});
