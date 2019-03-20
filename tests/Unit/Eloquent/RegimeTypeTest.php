<?php

namespace Tests\Unit\Eloquent;

use App\Models\RegimeType;
use Tests\EloquentTestCase;

class RegimeTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return RegimeType::class;
    }
}
