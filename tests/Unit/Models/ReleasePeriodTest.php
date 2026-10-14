<?php

use App\Models\LegacyStageType;
use Database\Factories\ReleasePeriodDateFactory;
use Database\Factories\ReleasePeriodFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('o período de lançamento descreve as datas e conhece o tipo de etapa', function () {
    $period = ReleasePeriodFactory::new()->create();
    ReleasePeriodDateFactory::new()->create(['release_period_id' => $period, 'start_date' => '2030-02-01', 'end_date' => '2030-02-10']);

    expect($period->getDatesArray())->toBe(['01/02/2030 a 10/02/2030'])
        ->and($period->stageType)->toBeInstanceOf(LegacyStageType::class);
});
