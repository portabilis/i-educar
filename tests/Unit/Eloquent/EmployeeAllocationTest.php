<?php

use App\Models\EmployeeAllocation;
use App\Models\LegacyPeriod;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EmployeeAllocation::class, relations: [
    'school' => LegacySchool::class,
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
    'period' => LegacyPeriod::class,
]));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->period->nome, $this->model->periodName);
});
