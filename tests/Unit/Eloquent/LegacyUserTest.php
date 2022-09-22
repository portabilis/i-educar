<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyUserTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyUser::class;
    }
}
