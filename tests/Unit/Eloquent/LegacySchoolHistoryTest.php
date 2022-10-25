<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolHistory;
use App\Models\LegacyStudent;
use Tests\EloquentTestCase;

class LegacySchoolHistoryTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolHistory::class;
    }
}
