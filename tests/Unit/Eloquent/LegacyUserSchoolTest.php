<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchool;
use App\Models\LegacyUser;
use App\Models\LegacyUserSchool;
use Tests\EloquentTestCase;

class LegacyUserSchoolTest extends EloquentTestCase
{
    protected $relations = [
        'school' => LegacySchool::class,
        'user' => LegacyUser::class,
    ];

    public function getEloquentModelName(): string
    {
        return LegacyUserSchool::class;
    }
}
