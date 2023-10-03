<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeeWithdrawal;
use App\Models\File;
use App\Models\LegacyUser;
use App\Models\WithdrawalReason;
use Tests\EloquentTestCase;

class EmployeeWithdrawalTest extends EloquentTestCase
{
    protected $relations = [
        'employee' => Employee::class,
        'reason' => WithdrawalReason::class,
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
        'files' => [File::class, ['relation_type' => EmployeeWithdrawal::class]],
    ];

    protected function getEloquentModelName(): string
    {
        return EmployeeWithdrawal::class;
    }
}
