<?php

namespace Tests\Unit\Eloquent;

use App\Models\File;
use App\Models\FileRelation;
use Tests\EloquentTestCase;

class FileRelationTest extends EloquentTestCase
{
    protected $relations = [
        'file' => File::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return FileRelation::class;
    }
}
