<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEducationType;
use Tests\EloquentTestCase;

class LegacyEducationTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEducationType::class;
    }
}
