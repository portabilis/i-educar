<?php

use App\Models\Employee;
use App\Models\LegacyUser;
use App\Models\PerformanceEvaluation;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(PerformanceEvaluation::class, relations: [
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
    'employee' => Employee::class,
]));

EloquentModelTestCase::registerModelTests();
