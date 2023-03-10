<?php

namespace Tests\Unit\Eloquent;

use App\Models\Backup;
use Tests\EloquentTestCase;

class BackupTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Backup::class;
    }
}
