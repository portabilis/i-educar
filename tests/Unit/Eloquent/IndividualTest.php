<?php

namespace Tests\Unit\Eloquent;

use App\Models\Individual;
use Tests\EloquentTestCase;

class IndividualTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Individual::class;
    }
}
