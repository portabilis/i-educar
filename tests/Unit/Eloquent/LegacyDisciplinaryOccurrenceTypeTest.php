<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyDisciplinaryOccurrenceTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplinaryOccurrenceType::class;
    }
}
