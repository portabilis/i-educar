<?php

use App\Rules\CheckSchoolClassTimes;

beforeEach(function () {
    $this->rule = new CheckSchoolClassTimes;
    $this->form = fn (array $overrides = []) => (object) ($overrides + [
        'hora_inicial' => '07:00',
        'hora_final' => '11:00',
        'hora_inicio_intervalo' => '09:00',
        'hora_fim_intervalo' => '09:20',
    ]);
});

test('aceita horários em ordem cronológica', function () {
    expect($this->rule->passes('schoolClass', ($this->form)()))->toBeTrue()
        ->and($this->rule->passes('schoolClass', ($this->form)(['hora_inicial' => '07:00:00', 'hora_final' => '11:00:00'])))->toBeTrue();
});

test('aceita horários não informados', function () {
    expect($this->rule->passes('schoolClass', (object) []))->toBeTrue()
        ->and($this->rule->passes('schoolClass', ($this->form)(['hora_final' => null])))->toBeTrue()
        ->and($this->rule->passes('schoolClass', ($this->form)(['hora_inicio_intervalo' => '', 'hora_fim_intervalo' => ''])))->toBeTrue();
});

test('recusa horário final anterior ou igual ao inicial', function (array $form, string $message) {
    expect($this->rule->passes('schoolClass', ($this->form)($form)))->toBeFalse()
        ->and($this->rule->message())->toBe($message);
})->with([
    'turma' => [['hora_inicial' => '14:00', 'hora_final' => '10:00'], 'A hora final deve ser posterior à hora inicial.'],
    'turma com horários iguais' => [['hora_inicial' => '10:00', 'hora_final' => '10:00'], 'A hora final deve ser posterior à hora inicial.'],
    'intervalo' => [['hora_inicio_intervalo' => '09:20', 'hora_fim_intervalo' => '09:00'], 'A hora final do intervalo deve ser posterior à hora de início do intervalo.'],
    'matutino' => [['hora_inicial_matutino' => '11:00', 'hora_final_matutino' => '07:00'], 'A hora final do turno matutino deve ser posterior à hora inicial do turno matutino.'],
    'vespertino' => [['hora_inicial_vespertino' => '17:00', 'hora_final_vespertino' => '13:00'], 'A hora final do turno vespertino deve ser posterior à hora inicial do turno vespertino.'],
]);

test('recusa intervalo fora do horário da turma', function (array $form) {
    expect($this->rule->passes('schoolClass', ($this->form)($form)))->toBeFalse()
        ->and($this->rule->message())->toBe('O intervalo deve estar entre a hora inicial e a hora final da turma.');
})->with([
    'começa antes' => [['hora_inicio_intervalo' => '06:30', 'hora_fim_intervalo' => '07:30']],
    'termina depois' => [['hora_inicio_intervalo' => '10:50', 'hora_fim_intervalo' => '11:10']],
]);
