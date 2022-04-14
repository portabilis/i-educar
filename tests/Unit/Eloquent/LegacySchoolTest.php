<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use Tests\EloquentTestCase;

class LegacySchoolTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchool::class;
    }
}
