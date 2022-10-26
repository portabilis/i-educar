<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolHistory;
use App\Models\LegacyStudent;
use Tests\EloquentTestCase;

class LegacySchoolHistoryTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolHistory::class;
    }
}
