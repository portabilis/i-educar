<?php

namespace Tests\Unit\Eloquent;

use App\Models\ManagerRole;
use App\Models\SchoolManager;
use Tests\EloquentTestCase;

class ManagerRoleTest extends EloquentTestCase
{
    protected $relations = [
        'schoolManagers' => SchoolManager::class,
    ];

    protected function getEloquentModelName(): string
    {
        return ManagerRole::class;
    }
}
