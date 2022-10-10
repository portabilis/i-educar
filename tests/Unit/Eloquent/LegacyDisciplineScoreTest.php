<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyRegistrationScore;
use Tests\EloquentTestCase;

class LegacyDisciplineScoreTest extends EloquentTestCase
{
    protected $relations = [
        'registrationScore' => LegacyRegistrationScore::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineScore::class;
    }
}
