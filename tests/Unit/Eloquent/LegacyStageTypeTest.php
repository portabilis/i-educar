<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStageType;
use Tests\EloquentTestCase;

class LegacyStageTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStageType::class;
    }
}
