<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyProject;
use App\Models\LegacyStudentProject;
use Tests\EloquentTestCase;

class LegacyProjectTest extends EloquentTestCase
{
    protected $relations = [
        'studentProjects' => LegacyStudentProject::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return LegacyProject::class;
    }
}
