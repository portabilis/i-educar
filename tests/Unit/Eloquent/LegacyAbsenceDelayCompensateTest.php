<?php

use App\Models\Employee;
use App\Models\LegacyAbsenceDelayCompensate;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyAbsenceDelayCompensate::class, relations: [
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
    'school' => LegacySchool::class,
    'institution' => LegacyInstitution::class,
    'employee' => Employee::class,
]));

EloquentModelTestCase::registerModelTests();
