<?php

use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyStudentDescriptiveOpinion;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyGeneralDescriptiveOpinion::class, relations: [
    'studentDescriptiveOpinion' => LegacyStudentDescriptiveOpinion::class,
]));

EloquentModelTestCase::registerModelTests();
