<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyProject;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentProject;
use Tests\EloquentTestCase;

class LegacyStudentProjectTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
        'project' => LegacyProject::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudentProject::class;
    }
}
