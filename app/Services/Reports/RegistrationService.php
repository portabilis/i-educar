<?php

namespace App\Services\Reports;

class RegistrationService
{
    public static function frequencyTotal(bool $isGeneralAbsence, int $absenceTotal, float $courseHourAbsence, float $gradeWorkload, float $academicDays): float
    {
        if ($isGeneralAbsence) {
            $value = (($academicDays - $absenceTotal) * 100) / $academicDays;
            $valueStr = number_format($value, 4, '.', '');

            return bcdiv($valueStr, '1', 1);
        }

        if (empty($gradeWorkload)) {
            return 100.0;
        }

        $value = 100 - (($absenceTotal * ($courseHourAbsence * 100)) / $gradeWorkload);
        $valueStr = number_format($value, 4, '.', '');

        return bcdiv($valueStr, '1', 1);
    }

    public static function frequencyByDiscipline(int $absence, float $hourAbsence, float $disciplineWorkload): float
    {
        if ($absence && !empty($disciplineWorkload)) {
            $value = 100 - (($absence * $hourAbsence * 100) / $disciplineWorkload);
            $valueStr = number_format($value, 4, '.', '');

            return bcdiv($valueStr, 1, 1);
        }

        return 100.0;
    }
}
