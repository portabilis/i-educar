<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use App\Models\WithdrawalReason;
use Tests\EloquentTestCase;

class WithdrawalReasonTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return WithdrawalReason::class;
    }
}
