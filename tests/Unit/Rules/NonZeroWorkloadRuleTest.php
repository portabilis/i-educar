<?php

uses(Tests\TestCase::class);

use App\Rules\NonZeroWorkloadRule;
use Illuminate\Support\Facades\Validator;

test('rejeita cargas horarias invalidas, zeradas, negativas ou vazias', function ($value) {
    $validator = Validator::make(
        ['carga_horaria' => $value],
        ['carga_horaria' => ['required', new NonZeroWorkloadRule()]]
    );

    expect($validator->fails())->toBeTrue();
})->with([
    '00:00',
    '0',
    '-01:00',
    '-00:30',
    '00:00:00',
    null,
    '',
    '   ',
    'abc',
    'texto',
]);

test('aceita cargas horarias estritamente positivas e validas', function ($value) {
    $validator = Validator::make(
        ['carga_horaria' => $value],
        ['carga_horaria' => [new NonZeroWorkloadRule()]]
    );

    expect($validator->passes())->toBeTrue();
})->with([
    '00:01',
    '00:30',
    '01:00',
    '10:00',
    '40:30',
    '99:59',
]);