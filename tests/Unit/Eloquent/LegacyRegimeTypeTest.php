<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegimeType;
use Tests\EloquentTestCase;

class LegacyRegimeTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
        'courses' => LegacyCourse::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyRegimeType::class;
    }
}
