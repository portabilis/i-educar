<?php

namespace Tests\Unit\Eloquent;

use App\Menu;
use App\Models\LegacyMenuUserType;
use App\Models\LegacyUserType;
use Tests\EloquentTestCase;

class LegacyMenuUserTypeTest extends EloquentTestCase
{
    protected $relations = [
        'menus' => Menu::class,
        'userType' => LegacyUserType::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyMenuUserType::class;
    }
}
