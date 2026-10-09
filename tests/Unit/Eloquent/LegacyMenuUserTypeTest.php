<?php

use App\Menu;
use App\Models\LegacyMenuUserType;
use App\Models\LegacyUserType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyMenuUserType::class, relations: [
    'menus' => Menu::class,
    'userType' => LegacyUserType::class,
]));

EloquentModelTestCase::registerModelTests();
