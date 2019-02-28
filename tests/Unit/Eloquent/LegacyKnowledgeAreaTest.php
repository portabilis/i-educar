<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyKnowledgeArea;
use Tests\EloquentTestCase;

class LegacyKnowledgeAreaTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyKnowledgeArea::class;
    }
}
