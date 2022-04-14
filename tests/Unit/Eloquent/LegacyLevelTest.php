<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyLevel;
use Tests\EloquentTestCase;

class LegacyLevelTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyLevel::class;
    }
}
