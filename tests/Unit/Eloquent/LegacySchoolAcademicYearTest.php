<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use Tests\EloquentTestCase;

class LegacySchoolAcademicYearTest extends EloquentTestCase
{
    protected $relations = [
        'school' => LegacySchool::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return LegacySchoolAcademicYear::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'year' => 'ano'
        ];
    }

    public function testYearAttribute(): void
    {
        $this->assertEquals($this->model->ano, $this->model->year);
    }
}
