<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGrade;
use Tests\EloquentTestCase;

class LegacyGradeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyGrade::class;
    }
}
