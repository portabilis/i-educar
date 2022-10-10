<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Tests\EloquentTestCase;

class LegacyUserTest extends EloquentTestCase
{
    protected $relations = [
        'type' => LegacyUserType::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyUser::class;
    }
}
