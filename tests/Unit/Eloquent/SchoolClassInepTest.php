<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolClass;
use App\Models\SchoolClassInep;
use Tests\EloquentTestCase;

class SchoolClassInepTest extends EloquentTestCase
{
    protected $relations = [
        'schoolClass' => LegacySchoolClass::class,
    ];

    protected function getEloquentModelName(): string
    {
        return SchoolClassInep::class;
    }

    public function testNumberAttribute(): void
    {
        $this->assertEquals($this->model->cod_turma_inep, $this->model->number);
    }
}
