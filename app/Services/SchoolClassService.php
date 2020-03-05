<?php

namespace App\Services;

use App\Models\LegacyLevel;
use App\Models\LegacySchoolClass;
use DateTime;
use iEducar\Modules\SchoolClass\Period;

class SchoolClassService
{
    /**
     * Retorna se o nome está disponível para cadastro. Ignora a turma com ID
     * caso seja informado.
     *
     * @param string   $name         Nome da turma
     * @param int      $course       ID do curso
     * @param int      $level        ID da série
     * @param int      $school       ID da escola
     * @param int      $academicYear Ano letivo
     * @param int|null $idToIgnore   ID da turma que deve ser ignorado (opcional)
     *
     * @return bool
     */
    public function isAvailableName($name, $course, $level, $school, $academicYear, $idToIgnore = null)
    {
        $query = LegacySchoolClass::query()
            ->where('nm_turma', (string) $name)
            ->where('ref_ref_cod_serie', $level)
            ->where('ref_cod_curso', $course)
            ->where('ref_ref_cod_escola', $school)
            ->where('ano', $academicYear)
            ->where('visivel', true)
            ->where('ativo', 1);

        if ($idToIgnore) {
            $query->where('cod_turma', '!=', $idToIgnore);
        }

        $isAvailable = $query->count() === 0;

        return $isAvailable;
    }

    /**
     * Valida se é obrigatório preencher o boletim diferenciado da turma.
     * Caso a série tenha regra de avaliação diferenciada configurada
     *
     * @param integer $levelId
     * @param integer $academicYear
     * @return bool
     */
    public function isRequiredAlternativeReportCard($levelId, $academicYear): bool
    {
        $evaluationRule = LegacyLevel::findOrFail($levelId)->evaluationRules()
            ->wherePivot('ano_letivo', $academicYear)
            ->get()
            ->first();

        if (empty($evaluationRule->regra_diferenciada_id)) {
            return false;
        }

        return true;
    }

    /**
     * Retorna o provavel turno a partir do horário de início e fim
     *
     * @param string $startTime
     * @param string $endTime
     * @return string
     *
     * @throws \Exception
     */
    public function getPeriodByTime($startTime, $endTime)
    {
        $startTime = new \DateTime($startTime);
        $endTime = new \DateTime($endTime);

        if ($startTime < new \DateTime('13:00') && $endTime < new \DateTime('13:00')) {
            return Period::MORNING;
        }

        if ($startTime >= new \DateTime('13:00') && $endTime < new \DateTime('18:00')) {
            return Period::AFTERNOON;
        }

        if ($startTime >= new \DateTime('18:00')) {
            return Period::NIGTH;
        }

        return Period::FULLTIME;
    }
}
