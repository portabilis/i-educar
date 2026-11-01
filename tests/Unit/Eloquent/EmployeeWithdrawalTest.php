<?php

use App\Models\Employee;
use App\Models\EmployeeWithdrawal;
use App\Models\File;
use App\Models\LegacyUser;
use App\Models\WithdrawalReason;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EmployeeWithdrawal::class, relations: [
    'employee' => Employee::class,
    'reason' => WithdrawalReason::class,
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
    'files' => [File::class, ['relation_type' => EmployeeWithdrawal::class]],
]));

EloquentModelTestCase::registerModelTests();
