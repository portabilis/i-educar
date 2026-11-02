<?php

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionStage;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyExemptionStage::class, relations: [
    'exemption' => LegacyDisciplineExemption::class,
]));

EloquentModelTestCase::registerModelTests();
