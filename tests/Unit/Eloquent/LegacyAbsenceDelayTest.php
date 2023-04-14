<?php

namespace Tests\Unit\Eloquent;

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\LegacyAbsenceDelay;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyAbsenceDelayTest extends EloquentTestCase
{
    public $relations = [
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
        'school' => LegacySchool::class,
        'institution' => LegacyInstitution::class,
        'employee' => Employee::class,
        'employeeRole' => LegacyEmployeeRole::class,
    ];

    protected function getEloquentModelName()
    {
        return LegacyAbsenceDelay::class;
    }
}
