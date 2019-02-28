<?php

namespace Tests\Unit\Eloquent;

use App\Models\Registration;
use Tests\EloquentTestCase;

class RegistrationTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Registration::class;
    }
}
