<?php

namespace Tests\Unit\Eloquent;

use App\Models\Discipline;
use Tests\EloquentTestCase;

class DisciplineTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Discipline::class;
    }
}
