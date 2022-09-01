<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplinaryOccurrenceType;
use Tests\EloquentTestCase;

class LegacyDisciplinaryOccurrenceTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplinaryOccurrenceType::class;
    }
}
