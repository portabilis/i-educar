<?php

namespace Tests\Unit\Enum;

use App\Models\Nationality;
use Tests\EnumTestCase;

class NationalityTest extends EnumTestCase
{
    public function getEnumName(): string
    {
        return Nationality::class;
    }

    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Brasileira',
            2 => 'Naturalizado brasileiro',
            3 => 'Estrangeira',
        ];
    }
}
