<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineDependence;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App\Models\LegacyGrade;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentDescriptiveOpinion;
use Tests\EloquentTestCase;

class LegacyRegistrationTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'student' => LegacyStudent::class,
        'school' => LegacySchool::class,
        'grade' => LegacyGrade::class,
        'course' => LegacyCourse::class,
        'enrollments' => LegacyEnrollment::class,
        'activeEnrollments' => LegacyEnrollment::class,
        'exemptions' => LegacyDisciplineExemption::class,
        'studentDescriptiveOpinion' => LegacyStudentDescriptiveOpinion::class,
        'dependencies' => LegacyDisciplineDependence::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegistration::class;
    }
}
