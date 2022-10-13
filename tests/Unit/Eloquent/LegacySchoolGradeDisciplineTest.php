<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacySchoolGradeDiscipline;
use Tests\EloquentTestCase;

class LegacySchoolGradeDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'discipline' => LegacyDiscipline::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolGradeDiscipline::class;
    }
}
