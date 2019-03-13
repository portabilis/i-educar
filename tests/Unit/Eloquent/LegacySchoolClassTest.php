<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use Tests\EloquentTestCase;

class LegacySchoolClassTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'school' => LegacySchool::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClass::class;
    }
}
