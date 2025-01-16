<?php

namespace Tests\Unit\Services;

use App\Services\Reports\Util;
use Tests\TestCase;

class UtilServiceTest extends TestCase
{
    public function test_sum_times_collection()
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

    public function test_sum_times_array()
    {
        $collect = collect([
            '22:30',
            '22:29',
        ]);
        $this->assertEquals('44:59', Util::sumTimes($collect->toArray()));
    }

    public function test_sum_times_null()
    {
        $collect = collect();
        $this->assertEquals('00:00', Util::sumTimes($collect));
        $collect = collect(['00:00']);
        $this->assertEquals('00:00', Util::sumTimes($collect));
        $collect = null;
        $this->assertEquals('00:00', Util::sumTimes($collect));
    }

    public function test_format_workload()
    {
        $this->assertEquals('00:00', Util::formatWorkload(null));
        $this->assertEquals('00:00', Util::formatWorkload(0));
        $this->assertEquals('00:30', Util::formatWorkload(0.5));
        $this->assertEquals('23:24', Util::formatWorkload(23.4));
        $this->assertEquals('25:54', Util::formatWorkload(25.9));
        $this->assertEquals('10:00', Util::formatWorkload(9.999999));
    }

    public function test_format()
    {
        $this->assertEquals('0,0', Util::format(null));
        $this->assertEquals('0,0', Util::format(0));
        $this->assertEquals('0,0', Util::format('0'));
        $this->assertEquals('0,0', Util::format(''));
        $this->assertEquals('0,5', Util::format(0.5));
        $this->assertEquals('1,50', Util::format(1.5, 2));
        $this->assertEquals('1,5', Util::format(1.52, 1));
        $this->assertEquals('1,5', Util::format(1.55, 1));
        $this->assertEquals('1,5', Util::format(1.59, 1));
        $this->assertEquals('1,59', Util::format(1.599, 2));
        $this->assertEquals('1,5', Util::format('1,59', 1));
        $this->assertEquals('1,0', Util::format(1, 1));
        $this->assertEquals('1,0', Util::format('1', 1));
        $this->assertEquals('A', Util::format('A'));
        $this->assertEquals('A,B', Util::format('A,B'));
        $this->assertEquals('A.B', Util::format('A.B'));
        $this->assertEquals('1,5', Util::format(' 1,59', 1)); // Existe notas com espaços extras antes
    }

    public function test_float()
    {
        $this->assertEquals(0, Util::float(null));
        $this->assertEquals(0.5, Util::float('0,5'));
    }
}
