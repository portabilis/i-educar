<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEducationType;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyEducationTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEducationType::class;
    }
}
