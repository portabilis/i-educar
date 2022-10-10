<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEnrollment;
use App\Models\LegacyPeriod;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyEnrollmentTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'schoolClass' => LegacySchoolClass::class,
        'period' => LegacyPeriod::class,
        'createdBy' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEnrollment::class;
    }
}
