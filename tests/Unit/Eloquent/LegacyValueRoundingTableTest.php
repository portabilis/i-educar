<?php

use App\Models\LegacyRoundingTable;
use App\Models\LegacyValueRoundingTable;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyValueRoundingTable::class, relations: [
    'roundingTable' => LegacyRoundingTable::class,
]));

EloquentModelTestCase::registerModelTests();
