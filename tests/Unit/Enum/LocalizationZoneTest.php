<?php

namespace Tests\Unit\Enum;

use App\Models\LocalizationZone;
use Tests\EnumTestCase;

class LocalizationZoneTest extends EnumTestCase
{
    public function getEnumName(): string
    {
        return LocalizationZone::class;
    }

    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Urbana',
            2 => 'Rural',
        ];
    }
}
