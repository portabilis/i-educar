<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyValueRoundingTable;
use Tests\EloquentTestCase;

class LegacyValueRoundingTableTest extends EloquentTestCase
{
    public function getEloquentModelName()
    {
        return LegacyValueRoundingTable::class;
    }
}
