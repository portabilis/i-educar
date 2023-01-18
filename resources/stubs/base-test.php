<?php

namespace Tests\Pages\Intranet;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\LoginFirstUser;
use Tests\TestCase;

class BaseTestName extends TestCase
{
    use LoginFirstUser, DatabaseTransactions, WithoutMiddleware;

    public const ROUTE = 'ProjectRoute';

    public function testSuccessResponse()
    {
        $this->get(self::ROUTE)->assertSuccessful();
    }
}
