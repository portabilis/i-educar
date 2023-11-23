<?php

namespace Tests\Unit\Eloquent;

use App\Models\File;
use Tests\EloquentTestCase;

class FileTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return File::class;
    }
}
