<?php

namespace App\Models;

use App\Contracts\Enum;

class RegistryOrigin implements Enum
{
    public const MIGRATION = 1;
    public const REGISTRATION = 2;
    public const UNIFICATION = 3;
    public const OTHER = 4;

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
