<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPostalCodeAddressNeighborhood;
use Tests\ViewTestCase;

class LegacyPostalCodeAddressNeighborhoodTest extends ViewTestCase
{
    /**
     * @return string
     */
    protected function getViewModelName(): string
    {
        return LegacyPostalCodeAddressNeighborhood::class;
    }
}
