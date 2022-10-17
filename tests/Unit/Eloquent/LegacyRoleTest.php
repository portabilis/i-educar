<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRole;
use Tests\EloquentTestCase;

class LegacyRoleTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRole::class;
    }
}
