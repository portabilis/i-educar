<?php

namespace Tests\Unit\Reports;

use App\Services\Reports\Util;
use Tests\TestCase;

class UtilTest extends TestCase
{
    /**
     * @test
     */
    public function format_workload_null()
    {
        $this->assertEquals('00:00', Util::formatWorkload(null));
    }

    /**
     * @test
     */
    public function format_workload_minute5()
    {
        $this->assertEquals('00:05', Util::formatWorkload(0.08));
    }

    /**
     * @test
     */
    public function format_workload_minute10()
    {
        $this->assertEquals('00:10', Util::formatWorkload(0.16));
    }

    /**
     * @test
     */
    public function format_workload_minute15()
    {
        $this->assertEquals('00:15', Util::formatWorkload(0.25));
    }

    /**
     * @test
     */
    public function format_workload_minute20()
    {
        $this->assertEquals('00:20', Util::formatWorkload(0.33));
    }

    /**
     * @test
     */
    public function format_workload_minute25()
    {
        $this->assertEquals('00:25', Util::formatWorkload(0.41));
    }

    /**
     * @test
     */
    public function format_workload_minute30()
    {
        $this->assertEquals('00:30', Util::formatWorkload(0.5));
    }

    /**
     * @test
     */
    public function format_workload_minute35()
    {
        $this->assertEquals('00:35', Util::formatWorkload(0.58));
    }

    /**
     * @test
     */
    public function format_workload_minute40()
    {
        $this->assertEquals('00:40', Util::formatWorkload(0.67));
    }

    /**
     * @test
     */
    public function format_workload_minute45()
    {
        $this->assertEquals('00:45', Util::formatWorkload(0.75));
    }

    /**
     * @test
     */
    public function format_workload_minute50()
    {
        $this->assertEquals('00:50', Util::formatWorkload(0.83));
    }

    /**
     * @test
     */
    public function format_workload_minute55()
    {
        $this->assertEquals('00:55', Util::formatWorkload(0.91));
    }

    /**
     * @test
     */
    public function format_workload_minute60()
    {
        $this->assertEquals('01:00', Util::formatWorkload(1));
    }
}
