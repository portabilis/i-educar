<?php

namespace Tests\Unit\Eloquent;

use App\Models\Level;
use Tests\EloquentTestCase;

class LevelTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Level::class;
    }
}
