<?php

namespace App\Services;

use App\Models\LegacyLevel;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use Illuminate\Support\Facades\DB;

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
     *
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
     * Retorna o array com os calendários letivos das turmas informadas
     * Data inicial da primeira etapa e data final da última etapa
     *
     * @param array $schoolClassId
     *
     * @return array|null
     */
    public function getCalendars(array $schoolClassId)
    {
        return LegacySchoolClassStage::query()
            ->select([
                DB::raw('(SELECT min(data_inicio) FROM turma_modulo tm WHERE tm.ref_cod_turma = turma_modulo.ref_cod_turma) as start_date'),
                DB::raw('(SELECT max(data_fim) FROM turma_modulo tm WHERE tm.ref_cod_turma = turma_modulo.ref_cod_turma) as end_date')
            ])
            ->distinct()
            ->whereIn('ref_cod_turma', $schoolClassId)
            ->get();
    }
}
