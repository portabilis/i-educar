<?php

use App\Services\SchoolClass\PeriodService;
use iEducar\Modules\SchoolClass\Period;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->service = app(PeriodService::class);
});

test('morning period', function () {
    $period = $this->service->getPeriodByTime('08:00', '12:00');
    $this->assertEquals(Period::MORNING, $period);

    $period = $this->service->getPeriodByTime('06:00', '10:00');
    $this->assertEquals(Period::MORNING, $period);

    $period = $this->service->getPeriodByTime('10:00', '12:30');
    $this->assertEquals(Period::MORNING, $period);
});

test('afternoon period', function () {
    $period = $this->service->getPeriodByTime('13:00', '17:30');
    $this->assertEquals(Period::AFTERNOON, $period);

    $period = $this->service->getPeriodByTime('13:00', '15:00');
    $this->assertEquals(Period::AFTERNOON, $period);

    $period = $this->service->getPeriodByTime('13:00', '17:00');
    $this->assertEquals(Period::AFTERNOON, $period);
});

test('night period', function () {
    $period = $this->service->getPeriodByTime('18:00', '20:30');
    $this->assertEquals(Period::NIGTH, $period);

    $period = $this->service->getPeriodByTime('20:00', '21:00');
    $this->assertEquals(Period::NIGTH, $period);

    $period = $this->service->getPeriodByTime('18:30', '22:00');
    $this->assertEquals(Period::NIGTH, $period);
});

test('fulltime period', function () {
    $period = $this->service->getPeriodByTime('08:00', '17:00');
    $this->assertEquals(Period::FULLTIME, $period);

    $period = $this->service->getPeriodByTime('11:00', '19:00');
    $this->assertEquals(Period::FULLTIME, $period);

    $period = $this->service->getPeriodByTime('10:00', '15:00');
    $this->assertEquals(Period::FULLTIME, $period);
});
