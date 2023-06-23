<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassType;
use Tests\EloquentTestCase;

class LegacySchoolClassTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
        'schoolClasses' => LegacySchoolClass::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolClassType::class;
    }
}
