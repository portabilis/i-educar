<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Models\LegacyUser;
use App\Models\MigratedDiscipline;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(MigratedDiscipline::class, relations: [
    'oldDiscipline' => LegacyDiscipline::class,
    'newDiscipline' => LegacyDiscipline::class,
    'grade' => LegacyGrade::class,
    'createdBy' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();
