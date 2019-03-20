<?php

namespace Tests\Unit\App\Models;

use App\Models\Person;
use Tests\EloquentTestCase;

class PersonTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Person::class;
    }
}
