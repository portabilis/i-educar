<?php

namespace App\Services\SchoolClass;

use App\Models\LegacyLevel;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Rules\CanAlterSchoolClassGrade;
use App\Rules\CanCreateSchoolClass;
use App\Rules\CanDeleteTurma;
use App\Rules\CheckAlternativeReportCardExists;
use App\Rules\CheckMandatoryCensoFields;
use App\Rules\CheckSchoolClassExistsByName;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
     *
     * @see CheckSchoolClassExistsByName
     * @deprecated
     */
    public function isAvailableName($name, $course, $level, $school, $academicYear, $idToIgnore = null)
    {
        $query = LegacySchoolClass::query()
            ->where('nm_turma', (string)$name)
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
     * @param int $levelId
     * @param int $academicYear
     *
     * @return bool
     *
     * @see CheckAlternativeReportCardExists
     * @deprecated
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

    /**
     * @param LegacySchoolClass $schoolClass
     *
     * @return LegacySchoolClass
     *
     * @throws ValidationException
     */
    public function storeSchoolClass(LegacySchoolClass $schoolClass)
    {
        $this->validate($schoolClass);

        $schoolClass->save();

        return $schoolClass;
    }

    public function deleteSchoolClass(LegacySchoolClass $schoolClass)
    {
        validator(
            ['schoolClass' => $schoolClass],
            [
                'schoolClass' => [
                    new CanDeleteTurma()
                ]
            ]
        )->validate();

        $schoolClass->ativo = 0;
        $schoolClass->visivel = 0;
        $schoolClass->data_exclusao = now();
        $schoolClass->save();
    }

    /**
     * @param LegacySchoolClass $schoolClass
     *
     * @throws ValidationException
     */
    private function validate(LegacySchoolClass $schoolClass)
    {
        validator(
            ['schoolClass' => $schoolClass],
            [
                'schoolClass' => [
                    new CanCreateSchoolClass(),
                    new CanAlterSchoolClassGrade(),
                    new CheckMandatoryCensoFields(),
                    new CheckSchoolClassExistsByName(),
                    new CheckAlternativeReportCardExists()
                ]
            ]
        )->validate();
    }
}
