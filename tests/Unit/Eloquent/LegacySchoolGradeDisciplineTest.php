<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolGradeDiscipline;
use Tests\EloquentTestCase;

class LegacySchoolGradeDisciplineTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolGradeDiscipline::class;
    }
}
