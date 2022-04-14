<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineScore;
use Tests\EloquentTestCase;

class LegacyDisciplineScoreTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineScore::class;
    }
}
