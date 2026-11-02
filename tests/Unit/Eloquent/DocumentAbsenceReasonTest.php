<?php

use App\Models\DocumentAbsenceReason;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(DocumentAbsenceReason::class));

EloquentModelTestCase::registerModelTests();
