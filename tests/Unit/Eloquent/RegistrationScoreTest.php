<?php

namespace Tests\Unit\Eloquent;

use App\Models\RegistrationScore;
use Tests\EloquentTestCase;

class RegistrationScoreTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return RegistrationScore::class;
    }
}
