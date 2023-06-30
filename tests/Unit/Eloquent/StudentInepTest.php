<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudent;
use App\Models\StudentInep;
use Tests\EloquentTestCase;

class StudentInepTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
    ];

    protected function getEloquentModelName(): string
    {
        return StudentInep::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'student_id' => 'cod_aluno',
            'number' => 'cod_aluno_inep',
            'name' => 'nome_inep',
            'font' => 'fonte',
        ];
    }
}
