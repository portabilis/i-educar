<?php

use App\Models\ManagerRole;
use App\Models\SchoolManager;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(ManagerRole::class, relations: [
    'schoolManagers' => SchoolManager::class,
]));

EloquentModelTestCase::registerModelTests();
