<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyProject;
use Tests\EloquentTestCase;

class LegacyProjectTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyProject::class;
    }
}
