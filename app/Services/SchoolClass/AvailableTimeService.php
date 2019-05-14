<?php

namespace App\Services\SchoolClass;

use App\Models\LegacySchoolClass;

class AvailableTimeService
{
    /**
     * Retorna se matrícula não possui enturmação em horário conflitante com a turma enviada por parâmetro
     *
     * @param int      $studentId     ID do aluno
     * @param int      $schoolClassId ID da turma
     *
     * @return bool
     */
    public function isAvailable(int $studentId, int $schoolClassId)
    {
        $schoolClass = LegacySchoolClass::findOrFail($schoolClassId);

        if ($schoolClass->tipo_mediacao_didatico_pedagogico != 1) {
            return true;
        }

        $otherSchoolClass = LegacySchoolClass::where('cod_turma', '<>', $schoolClassId)
            ->where('ano', $schoolClass->ano)
            ->whereHas('enrollments', function($enrollmentsQuery) use ($studentId, $schoolClass) {
                $enrollmentsQuery->whereHas('registration', function($registrationQuery) use ($studentId, $schoolClass) {
                    $registrationQuery->where('ref_cod_aluno', $studentId);
                    $registrationQuery->where('ano', $schoolClass->ano);
                })->where('ativo', 1);
            })->get();

        foreach ($otherSchoolClass as $otherSchoolClass) {
            if ($this->schedulesMatch($schoolClass, $otherSchoolClass)) {
                return false;
            }
        }

        return true;
    }

    private function schedulesMatch(LegacySchoolClass $schoolClass, LegacySchoolClass $otherSchoolClass)
    {
        if ($otherSchoolClass->tipo_mediacao_didatico_pedagogico != 1) {
            return false;
        }

        if (empty($schoolClass->dias_semana) || empty($otherSchoolClass->dias_semana)) {
            return false;
        }

        $weekdaysMatches = array_intersect($schoolClass->dias_semana, $otherSchoolClass->dias_semana);
        if (empty($weekdaysMatches)) {
            return false;
        }

        return $schoolClass->hora_inicial <= $otherSchoolClass->hora_final && $schoolClass->hora_final >= $otherSchoolClass->hora_inicial;
    }
}