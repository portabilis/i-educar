<?php

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRegistrationDisciplinaryOccurrenceType::class, relations: [
    'registration' => LegacyRegistration::class,
    'disciplinarOccurrenceType' => LegacyDisciplinaryOccurrenceType::class,
    'createdByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();
