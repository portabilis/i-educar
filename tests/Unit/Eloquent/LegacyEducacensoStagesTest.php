<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEducacensoStages;
use Tests\EloquentTestCase;

class LegacyEducacensoStagesTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEducacensoStages::class;
    }
}
