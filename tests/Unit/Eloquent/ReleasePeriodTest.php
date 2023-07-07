<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStageType;
use App\Models\ReleasePeriod;
use App\Models\ReleasePeriodDate;
use Tests\EloquentTestCase;

class ReleasePeriodTest extends EloquentTestCase
{
    protected $relations = [
        'periodDates' => ReleasePeriodDate::class,
        'stageType' => LegacyStageType::class,
    ];

    protected function getEloquentModelName(): string
    {
        return ReleasePeriod::class;
    }

    public function testGetDatesArray(): void
    {
        $value = $this->model->getDatesArray();
        $dates = $this->model->periodDates;

        $expect = collect();
        foreach ($dates as $date) {
            $expect->push($date->start_date->format('d/m/Y') . ' a ' . $date->end_date->format('d/m/Y'));
        }

        $this->assertJsonStringEqualsJsonString($expect, collect($value));
    }
}
