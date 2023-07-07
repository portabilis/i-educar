<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchool;
use App\Models\SchoolInep;
use Tests\EloquentTestCase;

class SchoolInepTest extends EloquentTestCase
{
    protected $relations = [
        'school' => LegacySchool::class,
    ];

    protected function getEloquentModelName(): string
    {
        return SchoolInep::class;
    }

    public function testNumberAttribute(): void
    {
        $this->assertEquals($this->model->cod_escola_inep, $this->model->number);
    }
}
