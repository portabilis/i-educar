<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRoundingTable;
use App\Models\LegacyValueRoundingTable;
use Tests\EloquentTestCase;

class LegacyRoundingTableTest extends EloquentTestCase
{
    protected $relations = [
        'roundingValues' => LegacyValueRoundingTable::class,
    ];

    public function getEloquentModelName()
    {
        return LegacyRoundingTable::class;
    }
}
