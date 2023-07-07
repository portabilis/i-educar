<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRoundingTable;
use App\Models\LegacyValueRoundingTable;
use Tests\EloquentTestCase;

class LegacyValueRoundingTableTest extends EloquentTestCase
{
    protected $relations = [
        'roundingTable' => LegacyRoundingTable::class,
    ];

    public function getEloquentModelName(): string
    {
        return LegacyValueRoundingTable::class;
    }
}
