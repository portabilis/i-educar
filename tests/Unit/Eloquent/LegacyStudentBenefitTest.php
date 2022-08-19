<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudentBenefit;
use Tests\EloquentTestCase;

class LegacyStudentBenefitTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudentBenefit::class;
    }
}
