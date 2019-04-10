<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolClassType;
use Tests\EloquentTestCase;

class LegacySchoolClassTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClassType::class;
    }
}
