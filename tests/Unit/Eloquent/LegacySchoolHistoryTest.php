<?php

use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\LegacyStudent;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolHistory::class, relations: [
    'student' => LegacyStudent::class,
    'institution' => LegacyInstitution::class,
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
    'school' => LegacySchool::class,
    'disciplines' => LegacySchoolHistoryDiscipline::class,
]));

EloquentModelTestCase::registerModelTests();
