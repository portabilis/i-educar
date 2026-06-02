<?php

namespace App\Models\Enums;

enum ComponentBatchStatus: int
{
    case WAITING = 1;
    case RUNNING = 2;
    case COMPLETED = 3;
    case FAILED = 4;
    case RESTORED = 5;

    public function label(): string
    {
        return match ($this) {
            self::WAITING => 'Aguardando',
            self::RUNNING => 'Em execução',
            self::COMPLETED => 'Concluído',
            self::FAILED => 'Falhou',
            self::RESTORED => 'Restaurado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::WAITING => 'info',
            self::RUNNING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::RESTORED => 'info',
        };
    }
}
