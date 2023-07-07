<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\LegacyStudent;
use Tests\EloquentTestCase;

class LegacySchoolHistoryDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolHistoryDiscipline::class;
    }
}
