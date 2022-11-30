<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEducationLevel;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyEducationLevelTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEducationLevel::class;
    }
}
