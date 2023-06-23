<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPeriod;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use Tests\EloquentTestCase;

class LegacySchoolClassTeacherTest extends EloquentTestCase
{
    protected $relations = [
        'schoolClass' => LegacySchoolClass::class,
        'schoolClassTeacherDisciplines' => LegacySchoolClassTeacherDiscipline::class,
        'period' => LegacyPeriod::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolClassTeacher::class;
    }
}
