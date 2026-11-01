<?php

use App\Models\LegacyStudent;
use App\Models\StudentInep;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    StudentInep::class,
    relations: [
        'student' => LegacyStudent::class,
    ],
    legacyAttributes: [
        'student_id' => 'cod_aluno',
        'number' => 'cod_aluno_inep',
        'name' => 'nome_inep',
        'font' => 'fonte',
    ],
));

EloquentModelTestCase::registerModelTests();
