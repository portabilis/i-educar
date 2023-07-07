<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use Tests\EloquentTestCase;

class LegacySchoolAcademicYearTest extends EloquentTestCase
{
    protected $relations = [
        'school' => LegacySchool::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolAcademicYear::class;
    }

    public function testYearAttribute(): void
    {
        $this->assertEquals($this->model->ano, $this->model->year);
    }
}
