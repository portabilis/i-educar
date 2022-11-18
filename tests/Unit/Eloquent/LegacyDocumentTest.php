<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDocument;
use Tests\EloquentTestCase;

class LegacyDocumentTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDocument::class;
    }
}
