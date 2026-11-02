<?php

uses(Tests\TestCase::class);
use App\Rules\WorkloadLimitRule;
use Illuminate\Support\Facades\Validator;

test('aceita cargas horarias dentro do limite de 99:59', function ($value) {
    $validator = Validator::make(
        ['carga_horaria' => $value],
        ['carga_horaria' => [new WorkloadLimitRule()]]
    );

    expect($validator->passes())->toBeTrue();
})->with([
    '00:01',
    '00:30',
    '01:00',
    '10:00',
    '40:00',
    '50:30',
    '98:00',
    '99:00',
    '99:59',
]);

test('rejeita cargas horarias acima do limite estrito de 99:59', function ($value) {
    $validator = Validator::make(
        ['carga_horaria' => $value],
        ['carga_horaria' => [new WorkloadLimitRule()]]
    );

    expect($validator->fails())->toBeTrue();
})->with([
    '100:00',
    '100:01',
    '100:39',
    '101:00',
    '120:00',
    '150:30',
    '200:00',
]);