<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyInstitutionTest extends EloquentTestCase
{
    public $relations = [
        'generalConfiguration' => LegacyGeneralConfiguration::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyInstitution::class;
    }
}
