<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyStudentDescriptiveOpinion;
use Tests\EloquentTestCase;

class LegacyDisciplineDescriptiveOpinionTest extends EloquentTestCase
{
    protected $relations = [
        'studentDescriptiveOpinion' => LegacyStudentDescriptiveOpinion::class,
        'discipline' => LegacyDiscipline::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineDescriptiveOpinion::class;
    }
}
