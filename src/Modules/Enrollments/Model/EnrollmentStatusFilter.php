<?php

namespace iEducar\Modules\Enrollments\Model;

class EnrollmentStatusFilter
{
    public const EXCEPT_TRANSFERRED_OR_ABANDONMENT = 9;
    public const ALL = 10;

    public static function getDescriptiveValues()
    {
        return [
            1 => 'Aprovado',
            2 => 'Reprovado',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Abandono',
            9 => 'Exceto Transferidos/Abandono',
            self::ALL => 'Todas',
            12 => 'Aprovado com dependência',
            13 => 'Aprovado pelo conselho',
            14 => 'Reprovado por faltas',
            15 => 'Falecido'
        ];
    }
}
