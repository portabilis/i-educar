<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyBenefit;
use Tests\EloquentTestCase;

class LegacyBenefitTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyBenefit::class;
    }
}
