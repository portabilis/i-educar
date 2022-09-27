<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyUserType;
use Tests\EloquentTestCase;

class LegacyUserTypeTest extends EloquentTestCase
{
    public function getEloquentModelName()
    {
        return LegacyUserType::class;
    }
}
