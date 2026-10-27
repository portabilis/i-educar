<?php

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionType;
use App\Models\LegacyInstitution;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyExemptionType::class, relations: [
    'disciplineExemptions' => LegacyDisciplineExemption::class,
    'institution' => LegacyInstitution::class,
]));

EloquentModelTestCase::registerModelTests();
