<?php

use App\Models\LegacyDocument;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDocument::class));

EloquentModelTestCase::registerModelTests();
