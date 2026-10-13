<?php

use App\Models\LegacyProject;
use App\Models\LegacyStudentProject;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyProject::class, relations: [
    'studentProjects' => LegacyStudentProject::class,
]));

EloquentModelTestCase::registerModelTests();
