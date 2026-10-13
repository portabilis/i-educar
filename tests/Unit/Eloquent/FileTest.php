<?php

use App\Models\File;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(File::class));

EloquentModelTestCase::registerModelTests();
