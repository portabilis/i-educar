<?php

namespace Tests\Unit\Reports;

use App\Services\Reports\Util;
use Tests\TestCase;

class UtilTest extends TestCase
{
    /**
     * @test
     */
    public function formatWorkloadNull()
    {
        $this->assertEquals('00:00', Util::formatWorkload(null));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute5()
    {
        $this->assertEquals('00:05', Util::formatWorkload(0.08));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute10()
    {
        $this->assertEquals('00:10', Util::formatWorkload(0.16));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute15()
    {
        $this->assertEquals('00:15', Util::formatWorkload(0.25));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute20()
    {
        $this->assertEquals('00:20', Util::formatWorkload(0.33));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute25()
    {
        $this->assertEquals('00:25', Util::formatWorkload(0.41));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute30()
    {
        $this->assertEquals('00:30', Util::formatWorkload(0.5));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute35()
    {
        $this->assertEquals('00:35', Util::formatWorkload(0.58));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute40()
    {
        $this->assertEquals('00:40', Util::formatWorkload(0.67));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute45()
    {
        $this->assertEquals('00:45', Util::formatWorkload(0.75));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute50()
    {
        $this->assertEquals('00:50', Util::formatWorkload(0.83));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute55()
    {
        $this->assertEquals('00:55', Util::formatWorkload(0.91));
    }

    /**
     * @test
     */
    public function formatWorkloadMinute60()
    {
        $this->assertEquals('01:00', Util::formatWorkload(1));
    }
}
