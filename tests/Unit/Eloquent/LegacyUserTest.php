<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployee;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Tests\EloquentTestCase;

class LegacyUserTest extends EloquentTestCase
{
    protected $relations = [
        'type' => LegacyUserType::class,
        'createdByEmployee' => LegacyEmployee::class,
        'deletedByEmployee' => LegacyEmployee::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyUser::class;
    }
}
