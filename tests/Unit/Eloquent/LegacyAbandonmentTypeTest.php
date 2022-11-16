<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAbandonmentType;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyAbandonmentTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAbandonmentType::class;
    }
}
