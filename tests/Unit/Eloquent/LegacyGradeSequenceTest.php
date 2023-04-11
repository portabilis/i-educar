<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGrade;
use App\Models\LegacyGradeSequence;
use Tests\EloquentTestCase;

class LegacyGradeSequenceTest extends EloquentTestCase
{
    public $relations = [
        'from' => LegacyGrade::class,
        'to' => LegacyGrade::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyGradeSequence::class;
    }
}
