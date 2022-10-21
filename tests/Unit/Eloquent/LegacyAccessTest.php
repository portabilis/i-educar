<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAccess;
use Tests\EloquentTestCase;

class LegacyAccessTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAccess::class;
    }
}
