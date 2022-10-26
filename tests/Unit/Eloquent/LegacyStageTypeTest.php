<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyStageType;
use Tests\EloquentTestCase;

class LegacyStageTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStageType::class;
    }
}
