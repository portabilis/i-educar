<?php

use App\Models\LegacyStageType;
use App\Models\ReleasePeriod;
use App\Models\ReleasePeriodDate;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(ReleasePeriod::class, relations: [
    'periodDates' => ReleasePeriodDate::class,
    'stageType' => LegacyStageType::class,
]));

EloquentModelTestCase::registerModelTests();

test('get dates array', function () {
    $value = $this->model->getDatesArray();
    $dates = $this->model->periodDates;

    $expect = collect();
    foreach ($dates as $date) {
        $expect->push($date->start_date->format('d/m/Y') . ' a ' . $date->end_date->format('d/m/Y'));
    }

    $this->assertJsonStringEqualsJsonString($expect, collect($value));
});
