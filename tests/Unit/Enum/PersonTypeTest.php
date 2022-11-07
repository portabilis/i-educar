<?php

namespace Tests\Unit\Enum;

use App\Models\PersonType;
use Tests\EnumTestCase;

class PersonTypeTest extends EnumTestCase
{
    public function getEnumName(): string
    {
        return PersonType::class;
    }

    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Pessoa Física',
            2 => 'Pessoa Jurídica',
        ];
    }
}
