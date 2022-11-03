<?php

namespace Tests\Unit\Support\Database;

use App\Support\Database\DateSerializer;
use Carbon\Carbon;
use Tests\TestCase;

class DateSerializerTest extends TestCase
{
    use DateSerializer;

    private $instance;

    public function testTrait(): void
    {
        $expect = '2022-01-01 23:01:00';

        $this->assertEquals($expect, $this->serializeDate(Carbon::parse($expect)));
    }
}
