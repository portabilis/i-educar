<?php

namespace Tests\Unit\Eloquent;

use App\Models\DocumentAbsenceReason;
use Tests\EloquentTestCase;

class DocumentAbsenceReasonTest extends EloquentTestCase
{
    protected function getEloquentModelName(): string
    {
        return DocumentAbsenceReason::class;
    }
}
