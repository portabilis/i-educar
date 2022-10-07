<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineDependence;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App\Models\LegacyGrade;
use App\Models\LegacyLevel;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyStudent;
use Illuminate\Support\Collection;
use Tests\EloquentTestCase;

class LegacyRegistrationTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'student' => LegacyStudent::class,
        'level' => LegacyLevel::class,
        'grade' => LegacyGrade::class,
        'course' => LegacyCourse::class,
        'enrollments' => [LegacyEnrollment::class],
        'activeEnrollments' => [LegacyEnrollment::class],
        'exemptions' => [LegacyDisciplineExemption::class],
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegistration::class;
    }
}
