<?php

namespace Tests\Unit\Eloquent;

use App\Models\RegionalType;
use Tests\EloquentTestCase;

class RegionalTypeTest extends EloquentTestCase
{
    protected function getEloquentModelName(): string
    {
        return RegionalType::class;
    }
}
