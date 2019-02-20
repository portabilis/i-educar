<?php

namespace Tests\Unit\Eloquent;

use App\Models\KnowledgeArea;
use Tests\EloquentTestCase;

class KnowledgeAreaTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return KnowledgeArea::class;
    }
}
