<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyUserSchool;
use Tests\EloquentTestCase;

class LegacySchoolTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'institution' => LegacyInstitution::class,
        'academicYears' => LegacySchoolAcademicYear::class,
        'person' => LegacyPerson::class,
        'organization' => LegacyOrganization::class,
        'schoolUsers' => LegacyUserSchool::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchool::class;
    }
}
