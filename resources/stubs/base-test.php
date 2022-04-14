<?php

namespace Tests\Pages\Intranet;

use Tests\LoginFirstUser;
use Tests\TestCase;

class BaseTestName extends TestCase
{
    use LoginFirstUser;

    public const ROUTE = 'ProjectRoute';

    public function testSuccessResponse()
    {
        $this->withoutMiddleware();
        $this->get(self::ROUTE)->assertSuccessful();
    }
}
