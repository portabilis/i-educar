<?php

namespace App\Models\Enums;

enum DayOfWeek: int
{
    case SUNDAY = 1;
    case MONDAY = 2;
    case TUESDAY = 3;
    case WEDNESDAY = 4;
    case THURSDAY = 5;
    case FRIDAY = 6;
    case SATURDAY = 7;

    public function name(): string
    {
        return match ($this) {
            self::SUNDAY => 'Domingo',
            self::MONDAY => 'Segunda',
            self::TUESDAY => 'Terça',
            self::WEDNESDAY => 'Quarta',
            self::THURSDAY => 'Quinta',
            self::FRIDAY => 'Sexta',
            self::SATURDAY => 'Sábado'
        };
    }

    public function shortName(): string
    {
        return match ($this) {
            self::SUNDAY => 'Dom',
            self::MONDAY => 'Seg',
            self::TUESDAY => 'Ter',
            self::WEDNESDAY => 'Qua',
            self::THURSDAY => 'Qui',
            self::FRIDAY => 'Sex',
            self::SATURDAY => 'Sáb'
        };
    }
}
