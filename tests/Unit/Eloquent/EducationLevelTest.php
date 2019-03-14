<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducationLevel;
use Tests\EloquentTestCase;

class EducationLevelTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EducationLevel::class;
    }
}
