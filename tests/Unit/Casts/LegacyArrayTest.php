<?php

use App\Casts\LegacyArray;
use App\Models\LegacySchoolClass;
use Tests\TestCase;

uses(TestCase::class);

test('converte o array do banco em lista e a lista em array do banco', function () {
    $cast = new LegacyArray;
    $model = new LegacySchoolClass;

    expect($cast->get($model, 'dias_semana', '{2,3,4}', []))->toBe(['2', '3', '4'])
        ->and($cast->get($model, 'dias_semana', null, []))->toBe([])
        ->and($cast->set($model, 'dias_semana', [2, 3], []))->toBe('{2,3}');
});
