<?php

namespace Tests\Unit\Enum;

use App\Models\GuardianType;
use Tests\EnumTestCase;

class GuardianTypeTest extends EnumTestCase
{
    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Pai',
            2 => 'Mãe',
            3 => 'Pai e Mãe',
            4 => 'Outra pessoa',
        ];
    }

    protected function getEnumName(): string
    {
        return GuardianType::class;
    }
}
