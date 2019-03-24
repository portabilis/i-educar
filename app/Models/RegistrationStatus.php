<?php

namespace App\Models;

use App\Contracts\Enum;

class RegistrationStatus implements Enum
{
    const APPROVED = 1;
    const REPROVED = 2;
    const ONGOING = 3;
    const TRANSFERRED = 4;
    const RECLASSIFIED = 5;
    const ABANDONED = 6;
    const IN_EXAM = 7;
    const APPROVED_PAST_EXAM = 8;
    const APPROVED_WITHOUT_EXAM = 10;
    const PRE_REGISTRATION = 11;
    const APPROVED_WITH_DEPENDENCY = 12;
    const APPROVED_BY_BOARD = 13;
    const REPROVED_BY_ABSENCE = 14;
    const DECEASED = 15;

    public function getDescriptiveValues(): array
    {
        return [
            self::APPROVED => 'Aprovado',
            self::REPROVED => 'Retido',
            self::ONGOING => 'Cursando',
            self::TRANSFERRED => 'Transferido',
            self::RECLASSIFIED => 'Reclassificado',
            self::ABANDONED => 'Abandono',
            self::IN_EXAM => 'Em exame',
            self::APPROVED_PAST_EXAM => 'Aprovado após exame',
            self::APPROVED_WITHOUT_EXAM => 'Aprovado sem exame',
            self::PRE_REGISTRATION => 'Pré-matrícula',
            self::APPROVED_WITH_DEPENDENCY => 'Aprovado com dependência',
            self::APPROVED_BY_BOARD => 'Aprovado pelo conselho',
            self::REPROVED_BY_ABSENCE => 'Reprovado por faltas',
            self::DECEASED => 'Falecido',
        ];
    }
}
