<?php

namespace Tests\Unit\Services\SchoolClass;

use App\Services\SchoolClass\PeriodService;
use iEducar\Modules\SchoolClass\Period;
use Tests\TestCase;

class PeriodServiceTest extends TestCase
{
    /**
     * @var PeriodService
     */
    private $service;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(PeriodService::class);
    }

    public function testMorningPeriod()
    {
        $period = $this->service->getPeriodByTime('08:00', '12:00');
        $this->assertEquals(Period::MORNING, $period);

        $period = $this->service->getPeriodByTime('06:00', '10:00');
        $this->assertEquals(Period::MORNING, $period);

        $period = $this->service->getPeriodByTime('10:00', '12:30');
        $this->assertEquals(Period::MORNING, $period);
    }

    public function testAfternoonPeriod()
    {
        $period = $this->service->getPeriodByTime('13:00', '17:30');
        $this->assertEquals(Period::AFTERNOON, $period);

        $period = $this->service->getPeriodByTime('13:00', '15:00');
        $this->assertEquals(Period::AFTERNOON, $period);

        $period = $this->service->getPeriodByTime('13:00', '17:00');
        $this->assertEquals(Period::AFTERNOON, $period);
    }

    public function testNightPeriod()
    {
        $period = $this->service->getPeriodByTime('18:00', '20:30');
        $this->assertEquals(Period::NIGTH, $period);

        $period = $this->service->getPeriodByTime('20:00', '21:00');
        $this->assertEquals(Period::NIGTH, $period);

        $period = $this->service->getPeriodByTime('18:30', '22:00');
        $this->assertEquals(Period::NIGTH, $period);
    }

    public function testFulltimePeriod()
    {
        $period = $this->service->getPeriodByTime('08:00', '17:00');
        $this->assertEquals(Period::FULLTIME, $period);

        $period = $this->service->getPeriodByTime('11:00', '19:00');
        $this->assertEquals(Period::FULLTIME, $period);

        $period = $this->service->getPeriodByTime('10:00', '15:00');
        $this->assertEquals(Period::FULLTIME, $period);
    }
}
