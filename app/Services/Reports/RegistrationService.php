<?php

namespace App\Services\Reports;

class RegistrationService
{
    public static function frequencyTotal(int $absenceType, int $absenceTotal, float $courseHourAbsence, float $gradeWorkload, float $diasLetivos = 0): float
    {
        return bcdiv(100 - (($absenceTotal * ($courseHourAbsence * 100)) / $gradeWorkload), 1, 1);
    }

    public static function frequencyByDiscipline(int $absence, float $courseHourAbsence, float $disciplineWorkload): float
    {
        if ($absence) {
            return bcdiv(100 - (($absence * $courseHourAbsence * 100) / $disciplineWorkload), 1, 1);
        }

        return 100.0;
    }
}
