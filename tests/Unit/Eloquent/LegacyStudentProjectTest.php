<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudentProject;
use Tests\EloquentTestCase;

class LegacyStudentProjectTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudentProject::class;
    }
}
