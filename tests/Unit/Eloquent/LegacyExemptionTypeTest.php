<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionType;
use Tests\EloquentTestCase;

class LegacyExemptionTypeTest extends EloquentTestCase
{
    public $relations = [
        'disciplineExemptions' => [LegacyDisciplineExemption::class],
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyExemptionType::class;
    }
}
