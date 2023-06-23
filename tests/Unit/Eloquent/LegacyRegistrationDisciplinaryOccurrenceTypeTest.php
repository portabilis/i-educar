<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyRegistrationDisciplinaryOccurrenceTypeTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'disciplinarOccurrenceType' => LegacyDisciplinaryOccurrenceType::class,
        'createdByUser' => LegacyUser::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyRegistrationDisciplinaryOccurrenceType::class;
    }
}
