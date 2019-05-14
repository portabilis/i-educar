<?php

namespace App\Services\SchoolClass;

use App\Models\LegacySchoolClass;

class AvailableTimeService
{
    /**
     * Retorna se matrícula não possui enturmação em horário conflitante com a
     * turma enviada por parâmetro.
     *
     * @param int $studentId     ID do aluno
     * @param int $schoolClassId ID da turma
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
            ->whereHas('enrollments', function ($enrollmentsQuery) use ($studentId, $schoolClass) {
                $enrollmentsQuery->whereHas('registration', function ($registrationQuery) use ($studentId, $schoolClass) {
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

    /**
     * Retorna se os horários das turmas são conflitantes.
     *
     * @param LegacySchoolClass $schoolClass
     * @param LegacySchoolClass $otherSchoolClass
     *
     * @return bool
     */
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

        // Valida se o início e fim do ano letivo da turma de destino não está
        // entre o período de início e fim da turma da outra enturmação.

        $doesNotStartBetween = false === $schoolClass->begin_academic_year->between($otherSchoolClass->begin_academic_year, $otherSchoolClass->end_academic_year);
        $doesNotEndBetween = false === $schoolClass->end_academic_year->between($otherSchoolClass->begin_academic_year, $otherSchoolClass->end_academic_year);

        if ($doesNotStartBetween && $doesNotEndBetween) {
            return false;
        }

        // Caso os períodos do ano letivo sejam conflitantes, valida se os
        // horários se sobrepoem.

        return $schoolClass->hora_inicial <= $otherSchoolClass->hora_final && $schoolClass->hora_final >= $otherSchoolClass->hora_inicial;
    }
}
