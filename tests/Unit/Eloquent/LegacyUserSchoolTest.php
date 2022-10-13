<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyUserSchool;
use Tests\EloquentTestCase;

class LegacyUserSchoolTest extends EloquentTestCase
{
    public function getEloquentModelName()
    {
        return LegacyUserSchool::class;
    }
}
