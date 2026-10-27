<?php

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplinaryOccurrenceType::class, relations: [
    'institution' => LegacyInstitution::class,
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();
