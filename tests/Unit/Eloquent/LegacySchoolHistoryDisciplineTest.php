<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\LegacyStudent;
use Tests\EloquentTestCase;

class LegacySchoolHistoryDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
        'schoolHistory' => LegacySchoolHistory::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolHistoryDiscipline::class;
    }
}
