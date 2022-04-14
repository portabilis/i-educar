<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEducationLevel;
use Tests\EloquentTestCase;

class LegacyEducationLevelTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEducationLevel::class;
    }
}
