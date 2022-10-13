<?php

namespace Tests\Unit\Eloquent;

use App\Models\EducacensoInstitution;
use Tests\EloquentTestCase;

class EducacensoInstitutionTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EducacensoInstitution::class;
    }
}
