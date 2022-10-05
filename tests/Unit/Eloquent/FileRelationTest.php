<?php

namespace Tests\Unit\Eloquent;

use App\Models\FileRelation;
use Tests\EloquentTestCase;

class FileRelationTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return FileRelation::class;
    }
}
