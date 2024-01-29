<?php

namespace App\Models\Enums;

enum EducacensoImportStatus: int
{
    case WAITING = 1;
    case ERROR = 2;
    case SUCCESS = 3;

    public function name(): string
    {
        return match ($this) {
            self::WAITING => 'Processando',
            self::ERROR => 'O arquivo não pode ser importado',
            self::SUCCESS => 'Finalizada'
        };
    }
}
