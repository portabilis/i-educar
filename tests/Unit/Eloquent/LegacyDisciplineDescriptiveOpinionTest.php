<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyStudentDescriptiveOpinion;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplineDescriptiveOpinion::class, relations: [
    'studentDescriptiveOpinion' => LegacyStudentDescriptiveOpinion::class,
    'discipline' => LegacyDiscipline::class,
]));

EloquentModelTestCase::registerModelTests();
