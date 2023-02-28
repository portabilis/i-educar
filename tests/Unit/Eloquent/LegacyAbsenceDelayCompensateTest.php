<?php

namespace Tests\Unit\Eloquent;

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\LegacyAbsenceDelayCompensate;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyAbsenceDelayCompensateTest extends EloquentTestCase
{
    public $relations = [
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
        'school' => LegacySchool::class,
        'institution' => LegacyInstitution::class,
        'employee' => Employee::class,
    ];

    protected function getEloquentModelName()
    {
        return LegacyAbsenceDelayCompensate::class;
    }
}
