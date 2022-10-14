<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducacensoImport;
use App\Models\Individual;
use Tests\EloquentTestCase;

class EducacensoImportTest extends EloquentTestCase
{
    public $relations = [
        'user' => Individual::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EducacensoImport::class;
    }
}
