<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyQualification;
use Tests\EloquentTestCase;

class LegacyQualificationTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyQualification::class;
    }
}
