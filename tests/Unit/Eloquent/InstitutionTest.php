<?php

namespace Tests\Unit\Eloquent;

use App\Models\Institution;
use Tests\EloquentTestCase;

class InstitutionTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Institution::class;
    }
}
