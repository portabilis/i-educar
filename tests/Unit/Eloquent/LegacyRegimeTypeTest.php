<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegimeType;
use Tests\EloquentTestCase;

class LegacyRegimeTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegimeType::class;
    }
}
