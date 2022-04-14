<?php

namespace Tests\Unit;

use App\Services\RelocationDate\RelocationDateProvider;
use App\Services\RelocationDate\RelocationDateService;
use PHPUnit\Framework\TestCase;

class RelocationDateServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testRelocationDateWhenNull()
    {
        $institution = $this->getMockBuilder(RelocationDateProvider::class)
            ->getMock();

        $institution->expects($this->any())
            ->method('getRelocationDate')
            ->willReturn(null);

        $relocationDateService = new RelocationDateService($institution);
        $this->assertNull($relocationDateService->getRelocationDate('2021-03-28'));
    }

    public function testRelocationDateWhenDefined()
    {
        $institution = $this->getMockBuilder(RelocationDateProvider::class)
            ->getMock();

        $institution->expects($this->any())
            ->method('getRelocationDate')
            ->willReturn('2021-03-01');

        $relocationDateService = new RelocationDateService($institution);
        $this->assertEquals('2021-03-01', $relocationDateService->getRelocationDate('2021-03-28'));
    }

    public function testRelocationDateWhenDefinedWithDifferentYears()
    {
        $institution = $this->getMockBuilder(RelocationDateProvider::class)
            ->getMock();

        $institution->expects($this->any())
            ->method('getRelocationDate')
            ->willReturn('2020-03-01');

        $relocationDateService = new RelocationDateService($institution);
        $this->assertEquals('2021-03-01', $relocationDateService->getRelocationDate('2021-03-28'));
    }

    public function testRelocationDateWhenDefinedWithLeapYear()
    {
        $institution = $this->getMockBuilder(RelocationDateProvider::class)
            ->getMock();

        $institution->expects($this->any())
            ->method('getRelocationDate')
            ->willReturn('2020-02-29');

        $relocationDateService = new RelocationDateService($institution);
        $this->assertEquals('2021-02-28', $relocationDateService->getRelocationDate('2021-02-29'));
    }
}
