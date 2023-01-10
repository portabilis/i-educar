<?php

namespace App\Services\Report;

class RegistrationService
{
    public static function frequencyTotal(int $absenceType, int $absenceTotal, float $courseHourAbsence, float $gradeWorkload, float $diasLetivos = 0): float
    {
        return bcdiv(100 - (($absenceTotal * ($courseHourAbsence * 100)) / $gradeWorkload), 1, 1);

        /*if ($tipoPresenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $totalFalta = $this->getTotalFaltaGeral($faltaAlunoId);

            return bcdiv(((($diasLetivos - $totalFalta) * 100) / $diasLetivos), 1, 1);
        }

        $totalFalta = $this->getTotalFaltaPorComponente($faltaAlunoId);

        return bcdiv(100 - (($totalFalta * ($horaFalta * 100)) / $cargaHoraria), 1, 1);*/
    }

    public static function frequencyByDiscipline(int $absence, float $courseHourAbsence, float $disciplineWorkload): float
    {
        if ($absence) {
            return bcdiv(100 - (($absence * $courseHourAbsence * 100) / $disciplineWorkload), 1, 1);
        }

        return 100.0;

        /* if (!$totalFaltasComponente) {
             return 100.0;
         }

         if (empty($cargaHorariaComponente) || $cargaHorariaComponente == 0) {
             throw new DisciplinesWithoutInformedHoursException('Não foi possivel calcular a frequência, pois existem disciplinas sem carga horária informada.');
         }

         return bcdiv(100 - (($totalFaltasComponente * $horaFalta) / $cargaHorariaComponente), 1, 1);*/
    }
}
