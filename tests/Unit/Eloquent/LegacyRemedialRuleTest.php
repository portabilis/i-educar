<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRemedialRule;
use Tests\EloquentTestCase;

class LegacyRemedialRuleTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRemedialRule::class;
    }
}
