<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use Tests\EloquentTestCase;

class LegacyRegistrationDisciplinaryOccurrenceTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegistrationDisciplinaryOccurrenceType::class;
    }
}
