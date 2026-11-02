<?php

use App\Models\LegacyCalendarDay;
use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyCalendarDayReasonFactory;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
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

test('marca as turmas do dia, troca as marcações na edição e as apaga na exclusão', function () {
    $scenario = EnrollmentScenario::for($this->user, 2030);
    $alfa = $scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $beta = $scenario->schoolClass(['nm_turma' => 'Turma Beta']);
    $calendar = LegacyCalendarYearFactory::new()->create(['ref_cod_escola' => $scenario->school, 'ano' => 2030]);
    $reason = LegacyCalendarDayReasonFactory::new()->create(['ref_cod_escola' => $scenario->school]);
    $url = '/intranet/educar_calendario_dia_cad.php?' . http_build_query(['dia' => 10, 'mes' => 3, 'ano' => 2030, 'ref_cod_calendario_ano_letivo' => $calendar->getKey()]);
    $form = fn (array $overrides) => LegacyFields::from($this->get($url)->assertOk()->getContent(), $overrides);
    $marked = fn () => DB::table('modules.calendario_turma')->where('calendario_ano_letivo_id', $calendar->getKey())->where('mes', 3)->where('dia', 10)->pluck('turma_id')->sort()->values()->all();
    $redirect = 'educar_calendario_anotacao_lst.php?dia=10&mes=3&ano=2030&ref_cod_calendario_ano_letivo=' . $calendar->getKey();

    $this->post($url, $form(['ref_cod_calendario_dia_motivo' => $reason->getKey(), 'descricao' => 'Conselho', 'turmas' => [$alfa->getKey() => 'on']]))
        ->assertRedirect($redirect);

    expect($marked())->toBe([$alfa->getKey()]);

    $payload = $form(['descricao' => 'Conselho de classe', 'turmas' => [$beta->getKey() => 'on']]);
    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect($redirect);

    expect($marked())->toBe([$beta->getKey()])
        ->and(LegacyCalendarDay::query()->where('ref_cod_calendario_ano_letivo', $calendar->getKey())->where('dia', 10)->value('descricao'))->toBe('Conselho de classe');

    $this->post($url, $form(['tipoacao' => 'Excluir']))->assertRedirect($redirect);

    expect($marked())->toBe([])
        ->and(LegacyCalendarDay::query()->where('ref_cod_calendario_ano_letivo', $calendar->getKey())->where('dia', 10)->exists())->toBeFalse();
});
