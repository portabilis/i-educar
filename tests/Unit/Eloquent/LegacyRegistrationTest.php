<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use Tests\EloquentTestCase;

class LegacyRegistrationTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegistration::class;
    }
}
