<?php

namespace App\Services\SchoolClass;

use App\Models\SchoolClass;

require_once 'lib/App/Model/Educacenso/TipoMediacaoDidaticoPedagogico.php';

class AvailableTimeService
{
    /**
     * Retorna se matrícula não possui enturmação em horário conflitante com a turma enviada por parâmetro
     *
     * @param int      $studentId     ID do aluno
     * @param int      $classroomId   ID da turma
     *
     * @return bool
     */
    public function isAvailable($studentId, $classroomId)
    {
        $schoolClass = SchoolClass::find($classroomId);

        if ($schoolClass->tipo_mediacao_didatico_pedagogico != 1) {
            return true;
        }

        $otherSchoolClass = SchoolClass::where('cod_turma', '<>', $classroomId)
            ->whereHas('enrollments', function($enrollmentsQuery) use ($studentId){
                $enrollmentsQuery->whereHas('registration', function($registrationQuery) use ($studentId) {
                    $registrationQuery->where('ref_cod_aluno', $studentId);
                })->where('ativo', 1);
            })->get();

        foreach ($otherSchoolClass as $otherSchoolClass) {
            if ($this->schedulesMatch($schoolClass, $otherSchoolClass)) {
                return false;
            }
        }

        return true;
    }

    private function schedulesMatch(SchoolClass $schoolClass, SchoolClass $otherSchoolClass)
    {
        if ($otherSchoolClass->tipo_mediacao_didatico_pedagogico != 1) {
            return false;
        }

        if (!is_array($schoolClass->dias_semana) || !is_array($otherSchoolClass->dias_semana)) {
            return false;
        }

        if (count(array_intersect($schoolClass->dias_semana, $otherSchoolClass->dias_semana)) > 0) {
            return $schoolClass->hora_inicial <= $otherSchoolClass->hora_final && $schoolClass->hora_final >= $otherSchoolClass->hora_inicial;
        } else {
            return false;
        }
    }
}