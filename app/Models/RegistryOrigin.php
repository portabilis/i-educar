<?php

namespace App\Models;

use App\Contracts\Enum;

class RegistryOrigin implements Enum
{
    const MIGRATION = 1;
    const REGISTRATION = 2;
    const UNIFICATION = 3;
    const OTHER = 4;

    public function getDescriptiveValues(): array
    {
        return [
            self::MIGRATION => 'Migração',
            self::REGISTRATION => 'Cadastro',
            self::UNIFICATION => 'Unificação',
            self::OTHER => 'Outro',
        ];
    }
}
