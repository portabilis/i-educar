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

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyScoreExam::class;
    }
}
