<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducacensoImport;
use Tests\EloquentTestCase;

class EducacensoImportTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EducacensoImport::class;
    }
}
