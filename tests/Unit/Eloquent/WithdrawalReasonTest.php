<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\WithdrawalReason;
use Tests\EloquentTestCase;

class WithdrawalReasonTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return WithdrawalReason::class;
    }
}
