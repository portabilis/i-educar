<?php

namespace Tests\Unit\Enum;

use App\Models\RegistryOrigin;
use Tests\EnumTestCase;

class RegistryOriginTest extends EnumTestCase
{
    protected function getEnumName(): string
    {
        return RegistryOrigin::class;
    }

    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Migração',
            2 => 'Cadastro',
            3 => 'Unificação',
            4 => 'Outro',
        ];
    }
}
