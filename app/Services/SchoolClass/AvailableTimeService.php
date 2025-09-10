<?php

namespace App\Services\SchoolClass;

use App\Models\LegacyEnrollment;
use App\Models\LegacySchoolClass;
use DateTime;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\SchoolClass\Period;

class AvailableTimeService
{
    /**
     * @var DateTime
     */
    private $enrollmentDate;

    /**
     * @var bool
     */
    private $onlySchoolClassesInformedOnCensus = false;

    /**
     * Retorna se matrícula não possui enturmação em horário conflitante com a
     * turma enviada por parâmetro.
     *
     * @param int $studentId     ID do aluno
     * @param int $schoolClassId ID da turma
     * @param int|null $turnoId  ID do turno da enturmação
     * @return bool
     */
    public function isAvailable(int $studentId, int $schoolClassId, ?int $turnoId = null)
    {
        $schoolClass = LegacySchoolClass::findOrFail($schoolClassId);

        if ($schoolClass->tipo_mediacao_didatico_pedagogico != 1 || ($this->onlySchoolClassesInformedOnCensus && $schoolClass->nao_informar_educacenso == 1)) {
            return true;
        }

        $schoolClassQuery = LegacySchoolClass::where('cod_turma', '<>', $schoolClassId)
            ->where('ano', $schoolClass->ano)
            ->whereHas('enrollments', function ($enrollmentsQuery) use ($studentId, $schoolClass) {
                $enrollmentsQuery->whereHas('registration', function ($registrationQuery) use ($studentId, $schoolClass) {
                    $registrationQuery->where('ref_cod_aluno', $studentId);
                    $registrationQuery->where('ano', $schoolClass->ano);
                    $registrationQuery->where('aprovado', 3);
                    $registrationQuery->where('ativo', 1);
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
            if ($this->schedulesMatch($schoolClass, $oneSchoolClass, $studentId, $turnoId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Define a data limite das enturmações que serão consideradas como
     * conflitantes.
     *
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
     *
     * @return bool
     */
    private function schedulesMatch(LegacySchoolClass $schoolClass, LegacySchoolClass $otherSchoolClass, int $studentId, $turnoId = null)
    {
        // O aluno pode ter matrícula em duas turmas no mesmo horário desde que:
        //
        // - Uma turma seja de Escolarização e a outra seja de Atendimento
        //   educacional especializado - AEE.

        if ($this->hasEscolarizacaoAndAee($schoolClass, $otherSchoolClass)) {
            return false;
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

        $doesNotStartBetween = $schoolClass->begin_academic_year->between($otherSchoolClass->begin_academic_year, $otherSchoolClass->end_academic_year) === false;
        $doesNotEndBetween = $schoolClass->end_academic_year->between($otherSchoolClass->begin_academic_year, $otherSchoolClass->end_academic_year) === false;

        if ($doesNotStartBetween && $doesNotEndBetween) {
            return false;
        }

        // Caso os períodos do ano letivo sejam conflitantes, valida se os
        // horários se sobrepoem.

        return $this->overlappingSchedules($schoolClass, $otherSchoolClass, $studentId, $turnoId);
    }

    private function overlappingSchedules(LegacySchoolClass $schoolClass, LegacySchoolClass $otherSchoolClass, int $studentId, $turnoId = null)
    {
        $horaInicial = $schoolClass->hora_inicial;
        $horaFinal = $schoolClass->hora_final;

        $otherHoraFinal = $otherSchoolClass->hora_final;
        $otherHoraInicial = $otherSchoolClass->hora_inicial;

        if (
            !is_null($turnoId) && // Se o turno for diferente de nulo
            $schoolClass->turma_turno_id === Period::FULLTIME && // Se a turma for integral
            $schoolClass->turma_turno_id !== $turnoId && // Se a enturmação é não é integral
            in_array($turnoId, [
                Period::MORNING,
                Period::AFTERNOON,
            ], true) // Se a enturmação é parcial
        ) {
            // Nesses cenários precisamos alterar o horário de comparação para o horário parcial
            if ($turnoId == Period::MORNING) {
                $horaInicial = $schoolClass->hora_inicial_matutino;
                $horaFinal = $schoolClass->hora_final_matutino;
            } elseif ($turnoId == Period::AFTERNOON) {
                $horaInicial = $schoolClass->hora_inicial_vespertino;
                $horaFinal = $schoolClass->hora_final_vespertino;
            }
        }

        if ($otherSchoolClass->turma_turno_id === Period::FULLTIME) {
            $turnoMatricula = LegacyEnrollment::query()
                ->where('ref_cod_turma', $otherSchoolClass->cod_turma)
                ->whereHas('registration', function ($query) use ($studentId, $otherSchoolClass) {
                    $query->where('ref_cod_aluno', $studentId);
                    $query->where('ano', $otherSchoolClass->ano);
                    $query->where('ativo', 1);
                    $query->where('aprovado', 3);
                })
                ->where('ativo', 1)
                ->value('turno_id');

            if ($turnoMatricula == Period::MORNING) {
                $otherHoraInicial = $otherSchoolClass->hora_inicial_matutino;
                $otherHoraFinal = $otherSchoolClass->hora_final_matutino;
            } elseif ($turnoMatricula == Period::AFTERNOON) {
                $otherHoraInicial = $otherSchoolClass->hora_inicial_vespertino;
                $otherHoraFinal = $otherSchoolClass->hora_final_vespertino;
            }
        }

        $startBefore = $horaInicial <= $otherHoraFinal;
        $endAfter = $horaFinal >= $otherHoraInicial;

        return $startBefore && $endAfter;
    }

    /**
     * Retorna true caso uma das turmas for Curricular (etapa de ensino) e a outra Atendimento educacional especializado - AEE
     *
     *
     * @return bool
     */
    private function hasEscolarizacaoAndAee(LegacySchoolClass $schoolClass, LegacySchoolClass $otherSchoolClass)
    {
        $tipoAtendimento = transformStringFromDBInArray($schoolClass->tipo_atendimento);
        $otherTipoAtendimento = transformStringFromDBInArray($otherSchoolClass->tipo_atendimento);

        if (is_array($tipoAtendimento) && is_array($otherTipoAtendimento) &&
            in_array(TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO, $tipoAtendimento) &&
            in_array(TipoAtendimentoTurma::AEE, $otherTipoAtendimento)) {
            return true;
        }

        if (is_array($tipoAtendimento) && is_array($otherTipoAtendimento) &&
            in_array(TipoAtendimentoTurma::AEE, $tipoAtendimento) &&
            in_array(TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO, $otherTipoAtendimento)) {
            return true;
        }

        return false;
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
