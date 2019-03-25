<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyStudent;
use Tests\EloquentTestCase;

class LegacyStudentTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudent::class;
    }
}
