<?php

use App\Models\ManagerLinkType;
use App\Models\SchoolManager;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(ManagerLinkType::class, relations: [
    'schoolManagers' => SchoolManager::class,
]));

EloquentModelTestCase::registerModelTests();
