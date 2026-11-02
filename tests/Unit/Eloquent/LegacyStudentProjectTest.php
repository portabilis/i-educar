<?php

use App\Models\LegacyProject;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentProject;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentProject::class, relations: [
    'student' => LegacyStudent::class,
    'project' => LegacyProject::class,
]));

EloquentModelTestCase::registerModelTests();
