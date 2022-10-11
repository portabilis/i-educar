<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducacensoDegree;
use Tests\EloquentTestCase;

class EducacensoDegreeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EducacensoDegree::class;
    }
}
