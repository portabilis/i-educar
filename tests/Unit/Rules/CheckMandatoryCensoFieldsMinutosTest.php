<?php

use App\Rules\CheckMandatoryCensoFields;

function chamarValidaMinutos(array $campos): array
{
    $regra = new CheckMandatoryCensoFields();
    $resultado = $regra->validaMinutosHorario((object) $campos);

    return [$resultado, $regra->message()];
}

// caixa preta
it('valida horários com minutos dentro do intervalo permitido e múltiplos de cinco', function ($horario) {
    [$resultado, $mensagem] = chamarValidaMinutos(['hora_inicial' => $horario]);

    expect($resultado)->toBeTrue();
})->with([
    'limite inferior do intervalo permitido (08:00)' => ['08:00'],
    'valor intermediário do intervalo permitido (08:30)' => ['08:30'],
    'limite superior do intervalo permitido (08:55)' => ['08:55'],
]);

it('rejeita horários com minutos fora dos critérios estabelecidos', function ($horario) {
    [$resultado, $mensagem] = chamarValidaMinutos(['hora_inicial' => $horario]);

    expect($resultado)->toBeFalse();
})->with([
    'minutos que não são múltiplos de cinco (08:04)' => ['08:04'],
    'valor de minutos acima do limite permitido (08:60)' => ['08:60'],
    'formato de horário inválido (0830)' => ['0830'],
]);

// caixa branca
it('permite a continuidade da validação quando o campo de horário está ausente ou vazio', function ($horario) {
    [$resultado, $mensagem] = chamarValidaMinutos(['hora_inicial' => $horario]);

    expect($resultado)->toBeTrue();
})->with([
    'Entrada com valor nulo' => [null],
    'Entrada com string vazia' => [''],
]);