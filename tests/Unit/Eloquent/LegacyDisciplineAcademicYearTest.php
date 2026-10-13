<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplineAcademicYear::class, relations: [
    'discipline' => LegacyDiscipline::class,
]));

EloquentModelTestCase::registerModelTests();
