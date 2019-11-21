<?php

namespace App\Services\SchoolClass;

use App\Models\LegacySchoolClass;
use DateTime;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

class AvailableTimeService
{
    /**
     * @var DateTime
     */
    private $enrollmentDate;

    /**
     * @var boolean
     */
    private $onlySchoolClassesInformedOnCensus = false;

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

        $schoolClassQuery = LegacySchoolClass::where('cod_turma', '<>', $schoolClassId)
            ->where('ano', $schoolClass->ano)
            ->whereHas('enrollments', function ($enrollmentsQuery) use ($studentId, $schoolClass) {
                $enrollmentsQuery->whereHas('registration', function ($registrationQuery) use ($studentId, $schoolClass) {
                    $registrationQuery->where('ref_cod_aluno', $studentId);
                    $registrationQuery->where('ano', $schoolClass->ano);
                    $registrationQuery->where('aprovado', 3);
                })->where('ativo', 1);

                if ($this->enrollmentDate) {
                    $enrollmentsQuery->where('data_enturmacao', '<', $this->enrollmentDate->format('Y-m-d'));
                }

            });

        if ($this->onlySchoolClassesInformedOnCensus) {
            $schoolClassQuery->where('nao_informar_educacenso', '<>', 1);
        }

        $otherSchoolClass = $schoolClassQuery->get();

        foreach ($otherSchoolClass as $oneSchoolClass) {
            if ($this->schedulesMatch($schoolClass, $oneSchoolClass)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Define a data limite das enturmações que serão consideradas como
     * conflitantes.
     *
     * @param DateTime $date
     *
     * @return $this
     */
    public function onlyUntilEnrollmentDate(DateTime $date)
    {
        $this->enrollmentDate = $date;

        return $this;
    }

    /**
     * Flag para somente considerar turmas que serão exportadas no Educacenso
     *
     * @return $this
     */
    public function onlySchoolClassesInformedOnCensus()
    {
        $this->onlySchoolClassesInformedOnCensus = true;

        return $this;
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
        // O aluno pode ter matrícula em duas turmas no mesmo horário desde que:
        //
        // - Uma turma seja de Escolarização e a outra seja de Atendimento
        //   educacional especializado - AEE.
        // - O horário de funcionamento da turma de escolarização seja igual ou
        //   superior a 7 horas diárias.

        if ($this->hasEscolarizacaoAndAee($schoolClass, $otherSchoolClass)) {
            $schoolClassEscolarizacao = $this->getSchoolClassEscolarizacao($schoolClass, $otherSchoolClass);

            if ($schoolClassEscolarizacao->getClassTime() >= 7) {
                return false;
            }
        }

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

        if (!$this->hasDates($schoolClass) || !$this->hasDates($otherSchoolClass)) {
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

        $startBefore = $schoolClass->hora_inicial <= $otherSchoolClass->hora_final;
        $endAfter = $schoolClass->hora_final >= $otherSchoolClass->hora_inicial;

        return $startBefore && $endAfter;
    }

    /**
     * Retorna true caso uma das turmas for Escolarização e a outra Atendimento educacional especializado - AEE
     *
     * @param LegacySchoolClass $schoolClass
     * @param LegacySchoolClass $otherSchoolClass
     *
     * @return bool
     */
    private function hasEscolarizacaoAndAee(LegacySchoolClass $schoolClass, LegacySchoolClass $otherSchoolClass)
    {
        if ($schoolClass->tipo_atendimento == TipoAtendimentoTurma::ESCOLARIZACAO &&
            $otherSchoolClass->tipo_atendimento == TipoAtendimentoTurma::AEE) {
            return true;
        }

        if ($schoolClass->tipo_atendimento == TipoAtendimentoTurma::AEE &&
            $otherSchoolClass->tipo_atendimento == TipoAtendimentoTurma::ESCOLARIZACAO) {
            return true;
        }

        return false;
    }

    /**
     * Recebe duas turmas e retorna a que tem o tipo de atendimento igual a Atendimento educacional especializado - AEE
     *
     * @param LegacySchoolClass $schoolClass
     * @param LegacySchoolClass $otherSchoolClass
     *
     * @return LegacySchoolClass
     */
    private function getSchoolClassEscolarizacao(LegacySchoolClass $schoolClass, LegacySchoolClass $otherSchoolClass)
    {
        if ($schoolClass->tipo_atendimento == TipoAtendimentoTurma::ESCOLARIZACAO) {
            return $schoolClass;
        }

        return $otherSchoolClass;
    }

    public function hasDates(LegacySchoolClass $schoolClass)
    {
        if (!$schoolClass->begin_academic_year) {
            return false;
        }

        if (!$schoolClass->end_academic_year) {
            return false;
        }

        return true;
    }
}
