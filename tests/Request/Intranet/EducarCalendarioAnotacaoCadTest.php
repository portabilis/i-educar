<?php

use App\Models\LegacyCalendarNote;
use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyCalendarDayNoteFactory;
use Database\Factories\LegacyCalendarNoteFactory;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

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

test('cadastra uma anotação no dia do calendário', function () {
    $url = '/intranet/educar_calendario_anotacao_cad.php?' . http_build_query($this->day);

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'nm_anotacao' => 'Anotação Beta',
        'descricao' => 'Descrição da anotação',
    ]);

    $this->post($url, $payload)->assertRedirect();

    expect(LegacyCalendarNote::query()->where('nm_anotacao', 'Anotação Beta')->exists())->toBeTrue();
});

test('edita e exclui a anotação do calendário', function () {
    $url = '/intranet/educar_calendario_anotacao_cad.php?cod_calendario_anotacao=' . $this->note->getKey() . '&' . http_build_query($this->day);

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['nm_anotacao' => 'Anotação renomeada']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();

    expect($this->note->refresh()->nm_anotacao)->toBe('Anotação renomeada');

    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
