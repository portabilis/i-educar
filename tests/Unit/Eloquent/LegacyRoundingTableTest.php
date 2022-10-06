<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRoundingTable;
use Tests\EloquentTestCase;

class LegacyRoundingTableTest extends EloquentTestCase
{
    public function getEloquentModelName()
    {
        return LegacyRoundingTable::class;
    }
}
