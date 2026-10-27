<?php

use App\Models\Employee;
use App\Models\LegacyIndividual;
use App\Models\LegacySchoolingDegree;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    LegacySchoolingDegree::class,
    relations: [
        'employees' => Employee::class,
        'individuals' => LegacyIndividual::class,
    ],
    legacyAttributes: [
        'id' => 'idesco',
        'description' => 'descricao',
        'schooling' => 'escolaridade',
    ],
));

EloquentModelTestCase::registerModelTests();
