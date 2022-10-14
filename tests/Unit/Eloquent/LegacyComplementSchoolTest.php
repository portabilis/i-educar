<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyComplementSchool;
use Tests\EloquentTestCase;

class LegacyComplementSchoolTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyComplementSchool::class;
    }
}
