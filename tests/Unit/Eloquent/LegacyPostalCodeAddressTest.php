<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPostalCodeAddress;
use Tests\ViewTestCase;

class LegacyPostalCodeAddressTest extends ViewTestCase
{
    protected function getViewModelName(): string
    {
        return LegacyPostalCodeAddress::class;
    }
}
