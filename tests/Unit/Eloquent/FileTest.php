<?php

namespace Tests\Unit\Eloquent;

use App\Models\File;
use App\Models\FileRelation;
use Tests\EloquentTestCase;

class FileTest extends EloquentTestCase
{
    protected $relations = [
        'relations' => FileRelation::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return File::class;
    }
}
