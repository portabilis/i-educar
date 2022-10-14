<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployeeRole;
use Tests\EloquentTestCase;

class LegacyEmployeeRoleTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEmployeeRole::class;
    }
}
