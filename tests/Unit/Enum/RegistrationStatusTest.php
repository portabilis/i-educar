<?php

namespace Tests\Unit\Enum;

use App\Models\RegistrationStatus;
use Tests\EnumTestCase;

class RegistrationStatusTest extends EnumTestCase
{
    protected function getEnumName(): string
    {
        return RegistrationStatus::class;
    }

    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Aprovado',
            2 => 'Retido',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Abandono',
            7 => 'Em exame',
            8 => 'Aprovado após exame',
            10 => 'Aprovado sem exame',
            11 => 'Pré-matrícula',
            12 => 'Aprovado com dependência',
            13 => 'Aprovado pelo conselho',
            14 => 'Reprovado por faltas',
            15 => 'Falecido',
        ];
    }
}
