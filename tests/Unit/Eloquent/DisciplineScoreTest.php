<?php

namespace Tests\Unit\Eloquent;

use App\Models\DisciplineScore;
use Tests\EloquentTestCase;

class DisciplineScoreTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return DisciplineScore::class;
    }
}
