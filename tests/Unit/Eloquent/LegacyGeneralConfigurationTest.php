<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyGeneralConfigurationTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyGeneralConfiguration::class;
    }
}
