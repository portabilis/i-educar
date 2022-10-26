<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyDisciplinaryOccurrenceTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplinaryOccurrenceType::class;
    }
}
