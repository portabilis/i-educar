<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPhone;
use Tests\EloquentTestCase;

class LegacyPhoneTest extends EloquentTestCase
{
    protected function getEloquentModelName()
    {
        return LegacyPhone::class;
    }
}
