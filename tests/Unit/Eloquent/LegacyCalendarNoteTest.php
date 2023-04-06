<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCalendarNote;
use Tests\EloquentTestCase;

class LegacyCalendarNoteTest extends EloquentTestCase
{
    protected function getEloquentModelName()
    {
        return LegacyCalendarNote::class;
    }
}
