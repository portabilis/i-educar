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

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return SchoolInep::class;
    }
}
