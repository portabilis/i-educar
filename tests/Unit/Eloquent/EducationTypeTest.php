<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducationType;
use Tests\EloquentTestCase;

class EducationTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EducationType::class;
    }
}
