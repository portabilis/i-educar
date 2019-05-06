<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyInstitutionTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyInstitution::class;
    }
}
