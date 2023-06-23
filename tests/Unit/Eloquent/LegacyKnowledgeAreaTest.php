<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyKnowledgeArea;
use Tests\EloquentTestCase;

class LegacyKnowledgeAreaTest extends EloquentTestCase
{
    protected $relations = [
        'institution' => LegacyInstitution::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyKnowledgeArea::class;
    }
}
