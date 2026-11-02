<?php

use App\Models\ManagerAccessCriteria;
use App\Models\SchoolManager;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(ManagerAccessCriteria::class, relations: [
    'schoolManagers' => SchoolManager::class,
]));

EloquentModelTestCase::registerModelTests();
