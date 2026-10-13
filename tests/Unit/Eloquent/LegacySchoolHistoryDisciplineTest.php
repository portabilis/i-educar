<?php

use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\LegacyStudent;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolHistoryDiscipline::class, relations: [
    'student' => LegacyStudent::class,
    'schoolHistory' => LegacySchoolHistory::class,
]));

EloquentModelTestCase::registerModelTests();
