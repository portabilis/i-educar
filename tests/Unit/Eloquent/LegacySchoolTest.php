<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchool;
use Tests\EloquentTestCase;

class LegacySchoolTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchool::class;
    }
}
