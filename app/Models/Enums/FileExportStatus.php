<?php

namespace App\Models\Enums;

enum FileExportStatus: int
{
    case WAITING = 1;
    case ERROR = 2;
    case SUCCESS = 3;

    public function name(): string
    {
        return match ($this) {
            self::WAITING => 'Aguardando a exportação do arquivo ser finalizada',
            self::ERROR => 'O arquivo não pode ser exportado',
            self::SUCCESS => 'Fazer download',
        };
    }
}
