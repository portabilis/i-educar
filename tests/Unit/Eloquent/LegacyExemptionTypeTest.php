<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionType;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyExemptionTypeTest extends EloquentTestCase
{
    public $relations = [
        'disciplineExemptions' => LegacyDisciplineExemption::class,
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyExemptionType::class;
    }
}
