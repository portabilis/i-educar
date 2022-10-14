<?php

namespace Tests\Unit\Eloquent;

use App\Models\WithdrawalReason;
use Tests\EloquentTestCase;

class WithdrawalReasonTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return WithdrawalReason::class;
    }
}
