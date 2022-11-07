<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyBenefit;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentBenefit;
use Tests\EloquentTestCase;

class LegacyStudentBenefitTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
        'benefit' => LegacyBenefit::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudentBenefit::class;
    }
}
