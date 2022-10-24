<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyMaritalStatus;
use Tests\EloquentTestCase;

class LegacyMaritalStatusTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyMaritalStatus::class;
    }
}
