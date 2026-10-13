<?php

use App\Models\LegacyGrade;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolClassGrade::class, relations: [
    'grade' => LegacyGrade::class,
    'schoolClass' => LegacySchoolClass::class,
]));

EloquentModelTestCase::registerModelTests();
