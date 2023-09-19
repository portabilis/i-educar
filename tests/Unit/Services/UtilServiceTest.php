<?php

namespace Tests\Unit\Services;

use App\Services\Reports\Util;
use Tests\TestCase;

class UtilServiceTest extends TestCase
{
    public function testSumTimesCollection()
    {
        $collect = collect(['22:30']);
        $this->assertEquals('22:30', Util::sumTimes($collect));
        $collect = collect([
            '22:30',
            '22:35',
            '00:01',
        ]);
        $this->assertEquals('45:06', Util::sumTimes($collect));
    }

    public function testSumTimesArray()
    {
        $collect = collect([
            '22:30',
            '22:29',
        ]);
        $this->assertEquals('44:59', Util::sumTimes($collect->toArray()));
    }

    public function testSumTimesNull()
    {
        $collect = collect();
        $this->assertEquals('00:00', Util::sumTimes($collect));
        $collect = collect(['00:00']);
        $this->assertEquals('00:00', Util::sumTimes($collect));
        $collect = null;
        $this->assertEquals('00:00', Util::sumTimes($collect));
    }

    public function testFormatWorkload()
    {
        $this->assertEquals('00:00', Util::formatWorkload(null));
        $this->assertEquals('00:00', Util::formatWorkload(0));
        $this->assertEquals('00:30', Util::formatWorkload(0.5));
        $this->assertEquals('23:24', Util::formatWorkload(23.4));
        $this->assertEquals('25:54', Util::formatWorkload(25.9));
    }

    public function testFormat()
    {
        $this->assertEquals('0,0', Util::format(null));
        $this->assertEquals('0,5', Util::format(0.5));
        $this->assertEquals('1,50', Util::format(1.5, 2));
    }

    public function testFloat()
    {
        $this->assertEquals(0, Util::float(null));
        $this->assertEquals(0.5, Util::float('0,5'));
    }
}
