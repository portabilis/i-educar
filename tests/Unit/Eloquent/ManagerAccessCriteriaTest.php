<?php

namespace Tests\Unit\Eloquent;

use App\Models\ManagerAccessCriteria;
use Tests\EloquentTestCase;

class ManagerAccessCriteriaTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return ManagerAccessCriteria::class;
    }
}
