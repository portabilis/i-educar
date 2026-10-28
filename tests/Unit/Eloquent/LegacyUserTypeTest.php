<?php

use App\Menu;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyUserType::class, relations: [
    'users' => LegacyUser::class,
    'menus' => Menu::class,
]));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->nivel, $this->model->level);
    $this->assertEquals($this->model->nm_tipo, $this->model->name);
    $this->assertEquals($this->model->descricao, $this->model->description);
    $this->assertEquals((bool) $this->model->ativo, $this->model->active);
});

test('get processes', function () {
    if ($this->model->level === LegacyUserType::LEVEL_ADMIN) {
        $except = collect(Menu::all()->pluck('id')->mapWithKeys(fn ($id) => [$id => LegacyUserType::CAN_REMOVE]));
    } else {
        $except = $this->model->menus()->get()->mapWithKeys(function ($menu) {
            $level = 0;

            if ($menu->pivot->visualiza ?? false) {
                $level = 1;
            }

            if ($menu->pivot->cadastra ?? false) {
                $level = 2;
            }

            if ($menu->pivot->exclui ?? false) {
                $level = 3;
            }

            return [$menu->id => $level];
        });
    }

    $this->assertJsonStringEqualsJsonString($except, $this->model->getProcesses());
});

test('get level descriptions', function () {
    $levels = [
        LegacyUserType::LEVEL_ADMIN => 'Poli-institucional',
        LegacyUserType::LEVEL_INSTITUTIONAL => 'Institucional',
        LegacyUserType::LEVEL_SCHOOLING => 'Escola',
        LegacyUserType::LEVEL_LIBRARY => 'Biblioteca',
    ];

    $except = collect($levels)->filter(fn ($value, $key) => $this->model->level <= $key);
    $this->assertJsonStringEqualsJsonString($except, $this->model->getLevelDescriptions());
});

test('constants', function () {
    $this->assertEquals(1, LegacyUserType::LEVEL_ADMIN);
    $this->assertEquals(2, LegacyUserType::LEVEL_INSTITUTIONAL);
    $this->assertEquals(4, LegacyUserType::LEVEL_SCHOOLING);
    $this->assertEquals(8, LegacyUserType::LEVEL_LIBRARY);
    $this->assertEquals(1, LegacyUserType::CAN_VIEW);
    $this->assertEquals(2, LegacyUserType::CAN_MODIFY);
    $this->assertEquals(3, LegacyUserType::CAN_REMOVE);
});
