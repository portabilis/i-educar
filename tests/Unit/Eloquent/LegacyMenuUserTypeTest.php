<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyMenuUserType;
use Tests\EloquentTestCase;

class LegacyMenuUserTypeTest extends EloquentTestCase
{
    protected function getEloquentModelName()
    {
        return LegacyMenuUserType::class;
    }
}
