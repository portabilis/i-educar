<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacyKnowledgeArea;
use Tests\EloquentTestCase;

class LegacyDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'knowledgeArea' => LegacyKnowledgeArea::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDiscipline::class;
    }
}
