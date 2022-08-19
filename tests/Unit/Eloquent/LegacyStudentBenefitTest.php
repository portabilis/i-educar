<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudentBenefit;
use Tests\EloquentTestCase;

class LegacyBenefitTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudentBenefit::class;
    }
}
