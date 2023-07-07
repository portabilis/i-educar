<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPeriod;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use Tests\EloquentTestCase;

class LegacyPeriodTest extends EloquentTestCase
{
    protected $relations = [
        'schoolClass' => LegacySchoolClass::class,
        'schoolClassTeacher' => LegacySchoolClassTeacher::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyPeriod::class;
    }

    /** @test */
    public function getNameAttribute(): void
    {
        $this->assertEquals($this->model->name, $this->model->nome);
    }
}
