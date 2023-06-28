<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyComplementSchool;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyComplementSchoolTest extends EloquentTestCase
{
    public $relations = [
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
        'school' => LegacySchool::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyComplementSchool::class;
    }
}
