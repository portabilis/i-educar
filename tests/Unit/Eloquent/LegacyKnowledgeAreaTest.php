<?php

use App\Models\LegacyInstitution;
use App\Models\LegacyKnowledgeArea;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyKnowledgeArea::class, relations: [
    'institution' => LegacyInstitution::class,
]));

EloquentModelTestCase::registerModelTests();
