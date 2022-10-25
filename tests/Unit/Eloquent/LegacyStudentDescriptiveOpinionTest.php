<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use App\Models\LegacyStudentDescriptiveOpinion;
use Tests\EloquentTestCase;

class LegacyStudentDescriptiveOpinionTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudentDescriptiveOpinion::class;
    }
}
