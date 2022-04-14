<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistrationScore;
use Tests\EloquentTestCase;

class LegacyRegistrationScoreTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegistrationScore::class;
    }
}
