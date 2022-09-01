<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAbandonmentType;
use Tests\EloquentTestCase;

class LegacyAbandonmentTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAbandonmentType::class;
    }
}
