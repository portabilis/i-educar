<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineDependence;
use Tests\EloquentTestCase;

class LegacyDisciplineDependenceTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineDependence::class;
    }
}
