<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPeriod;
use Tests\EloquentTestCase;

class LegacyPeriodTest extends EloquentTestCase
{
    private LegacyPeriod $period;

    protected function getEloquentModelName()
    {
        return LegacyPeriod::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->period = $this->createNewModel();
    }

    /** @test */
    public function getNameAttribute()
    {
        $this->assertEquals($this->period->getNameAttribute(), $this->period->nome);
    }
}
