<?php

namespace Tests\Unit\Eloquent;

use App\Menu;
use Tests\EloquentTestCase;

class MenuTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Menu::class;
    }
}
