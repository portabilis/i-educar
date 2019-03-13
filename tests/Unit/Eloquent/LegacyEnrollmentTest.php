<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEnrollment;
use Tests\EloquentTestCase;

class LegacyEnrollmentTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEnrollment::class;
    }
}
