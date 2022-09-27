<?php

namespace Tests\Unit\Eloquent;

use App\User;
use Tests\EloquentTestCase;

class UserTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        $this->markTestSkipped();
        return User::class;
    }
}
