<?php

namespace Tests\Unit\Eloquent;

use App\Models\ManagerLinkType;
use App\Models\SchoolManager;
use Tests\EloquentTestCase;

class ManagerLinkTypeTest extends EloquentTestCase
{
    protected $relations = [
        'schoolManagers' => SchoolManager::class,
    ];

    protected function getEloquentModelName(): string
    {
        return ManagerLinkType::class;
    }
}
