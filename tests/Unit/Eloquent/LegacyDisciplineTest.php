<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use Tests\EloquentTestCase;

class LegacyDisciplineTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDiscipline::class;
    }
}
