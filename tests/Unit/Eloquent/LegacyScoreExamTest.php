<?php

use App\Models\LegacyRegistration;
use App\Models\LegacyScoreExam;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    LegacyScoreExam::class,
    relations: [
        'registration' => LegacyRegistration::class,
    ],
    legacyAttributes: [
        'id' => 'idesco',
        'description' => 'descricao',
        'schooling' => 'escolaridade',
    ],
));

EloquentModelTestCase::registerModelTests();
