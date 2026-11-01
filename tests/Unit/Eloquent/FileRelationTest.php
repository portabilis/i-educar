<?php

use App\Models\File;
use App\Models\FileRelation;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(FileRelation::class, relations: [
    'file' => File::class,
]));

EloquentModelTestCase::registerModelTests();
