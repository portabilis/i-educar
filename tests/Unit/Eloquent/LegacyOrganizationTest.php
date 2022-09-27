<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyOrganization;
use Tests\EloquentTestCase;

class LegacyOrganizationTest extends EloquentTestCase
{
    protected function getEloquentModelName()
    {
        return LegacyOrganization::class;
    }
}
