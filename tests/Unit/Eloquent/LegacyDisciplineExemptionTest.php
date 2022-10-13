<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionStage;
use App\Models\LegacyExemptionType;
use App\Models\LegacyRegistration;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyDisciplineExemptionTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'discipline' => LegacyDiscipline::class,
        'type' => LegacyExemptionType::class,
        'stages' => [LegacyExemptionStage::class],
        'createdBy' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineExemption::class;
    }
}
