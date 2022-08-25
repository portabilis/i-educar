<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyTransferType;
use Tests\EloquentTestCase;

class LegacyTransferTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyTransferType::class;
    }
}
