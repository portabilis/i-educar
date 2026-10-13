<?php

use App\Models\EducacensoImport;
use App\Models\Individual;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(EducacensoImport::class, relations: [
    'user' => Individual::class,
]));

EloquentModelTestCase::registerModelTests();
