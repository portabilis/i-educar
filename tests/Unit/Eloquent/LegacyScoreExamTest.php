<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use App\Models\LegacyScoreExam;
use Tests\EloquentTestCase;

class LegacyScoreExamTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyScoreExam::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'idesco',
            'description' => 'descricao',
            'schooling' => 'escolaridade',
        ];
    }
}
