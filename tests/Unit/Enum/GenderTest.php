<?php

namespace Tests\Unit\Enum;

use App\Models\Gender;
use Tests\EnumTestCase;

class GenderTest extends EnumTestCase
{
    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Masculino',
            2 => 'Feminino',
        ];
    }

    protected function getEnumName(): string
    {
        return Gender::class;
    }
}
