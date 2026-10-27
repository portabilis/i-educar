<?php

use App\Services\RelocationDate\RelocationDateProvider;
use App\Services\RelocationDate\RelocationDateService;
use PHPUnit\Framework\TestCase;

uses(TestCase::class);

/**
 * A basic unit test example.
 *
 * @return void
 */
test('relocation date when null', function () {
    $institution = $this->getMockBuilder(RelocationDateProvider::class)
        ->getMock();

    $institution->expects($this->any())
        ->method('getRelocationDate')
        ->willReturn(null);

    $relocationDateService = new RelocationDateService($institution);
    $this->assertNull($relocationDateService->getRelocationDate('2021-03-28'));
});

test('relocation date when defined', function () {
    $institution = $this->getMockBuilder(RelocationDateProvider::class)
        ->getMock();

    $institution->expects($this->any())
        ->method('getRelocationDate')
        ->willReturn('2021-03-01');

    $relocationDateService = new RelocationDateService($institution);
    $this->assertEquals('2021-03-01', $relocationDateService->getRelocationDate('2021-03-28'));
});

test('relocation date when defined with different years', function () {
    $institution = $this->getMockBuilder(RelocationDateProvider::class)
        ->getMock();

    $institution->expects($this->any())
        ->method('getRelocationDate')
        ->willReturn('2020-03-01');

    $relocationDateService = new RelocationDateService($institution);
    $this->assertEquals('2021-03-01', $relocationDateService->getRelocationDate('2021-03-28'));
});

test('relocation date when defined with leap year', function () {
    $institution = $this->getMockBuilder(RelocationDateProvider::class)
        ->getMock();

    $institution->expects($this->any())
        ->method('getRelocationDate')
        ->willReturn('2020-02-29');

    $relocationDateService = new RelocationDateService($institution);
    $this->assertEquals('2021-02-28', $relocationDateService->getRelocationDate('2021-02-29'));
});
