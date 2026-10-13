<?php

use App\Models\LegacyCourse;
use App\Models\LegacySchool;
use App\Models\LegacySchoolCourse;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolCourse::class, relations: [
    'school' => LegacySchool::class,
    'course' => LegacyCourse::class,
]));

EloquentModelTestCase::registerModelTests();
