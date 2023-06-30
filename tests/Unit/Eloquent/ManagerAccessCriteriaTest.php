<?php

namespace Tests\Unit\Eloquent;

use App\Models\ManagerAccessCriteria;
use App\Models\SchoolManager;
use Tests\EloquentTestCase;

class ManagerAccessCriteriaTest extends EloquentTestCase
{
    protected $relations = [
        'schoolManagers' => SchoolManager::class,
    ];

    protected function getEloquentModelName(): string
    {
        return ManagerAccessCriteria::class;
    }
}
