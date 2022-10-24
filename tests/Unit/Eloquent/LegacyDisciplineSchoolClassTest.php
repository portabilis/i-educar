<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineSchoolClass;
use Tests\EloquentTestCase;

class LegacyDisciplineSchoolClassTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineSchoolClass::class;
    }
}
