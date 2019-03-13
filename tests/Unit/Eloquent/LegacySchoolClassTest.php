<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolClass;
use Tests\EloquentTestCase;

class LegacySchoolClassTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClass::class;
    }
}
