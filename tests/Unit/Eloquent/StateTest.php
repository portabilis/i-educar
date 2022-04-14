<?php

namespace Tests\Unit\Eloquent;

use App\Models\State;
use Tests\EloquentTestCase;

class StateTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return State::class;
    }
}
