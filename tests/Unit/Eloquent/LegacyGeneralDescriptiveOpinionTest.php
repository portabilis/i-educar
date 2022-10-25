<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyStudentDescriptiveOpinion;
use Tests\EloquentTestCase;

class LegacyGeneralDescriptiveOpinionTest extends EloquentTestCase
{
    protected $relations = [
        'studentDescriptiveOpinion' => LegacyStudentDescriptiveOpinion::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyGeneralDescriptiveOpinion::class;
    }
}
