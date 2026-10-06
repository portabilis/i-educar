<?php

namespace App\Services;

use App\Models\LegacyDiscipline;
use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Support\Collection;

class SchoolGradeDisciplineService
{
    /**
     * @param int $school
     * @param int $grade
     * @return LegacySchoolGradeDiscipline[]|Collection
     */
    public function getDisciplines($school, $grade)
    {
        return LegacySchoolGradeDiscipline::query()
            ->with('discipline')
            ->whereSchool($school)
            ->whereGrade($grade)
            ->get()
            ->pluck('discipline');
    }

    /**
     * @param int $school
     * @param int $grade
     * @return LegacySchoolGradeDiscipline[]|Collection
     */
    public function getAllDisciplines($school, $grade)
    {
        return LegacySchoolGradeDiscipline::query()
            ->whereSchool($school)
            ->whereGrade($grade)
            ->get();
    }

    public function store(
        $gradeId,
        $schoolId,
        $disciplineId,
        $active = null,
        $workload = null,
        $specificStages = null,
        $usedStages = null,
        $academicYears = [],
        $absenceHour = null,
        $weeklyClasses = null
    ): bool {
        if (!is_numeric($gradeId) || !is_numeric($schoolId) || !is_numeric($disciplineId)) {
            return false;
        }

        if (!LegacyDiscipline::query()->whereKey($disciplineId)->exists()) {
            return false;
        }

        $values = [
            'carga_horaria' => is_numeric($workload) ? $workload : null,
            'hora_falta' => is_numeric($absenceHour) ? $absenceHour : null,
            'aulas_por_semana' => is_numeric($weeklyClasses) ? $weeklyClasses : null,
            'etapas_utilizadas' => is_string($usedStages) ? $usedStages : null,
        ];

        if (is_numeric($specificStages)) {
            $values['etapas_especificas'] = $specificStages;
        } elseif (is_null($specificStages)) {
            $values['etapas_especificas'] = 0;
            $values['etapas_utilizadas'] = null;
        }

        if (is_array($academicYears)) {
            $values['anos_letivos'] = '{' . implode(',', array_map('intval', $academicYears)) . '}';
        }

        $query = LegacySchoolGradeDiscipline::query()
            ->whereGrade($gradeId)
            ->whereSchool($schoolId)
            ->whereDiscipline($disciplineId);

        if ((clone $query)->exists()) {
            if (is_numeric($active)) {
                $values['ativo'] = $active;
            }

            $query->update($values);

            return true;
        }

        $values['ref_ref_cod_serie'] = $gradeId;
        $values['ref_ref_cod_escola'] = $schoolId;
        $values['ref_cod_disciplina'] = $disciplineId;
        $values['ativo'] = 1;

        LegacySchoolGradeDiscipline::query()->create($values);

        return true;
    }

    public function getAcademicYearsByGrade($gradeId, $schoolIds): Collection
    {
        $schoolIds = collect($schoolIds)->filter(fn ($schoolId) => is_numeric($schoolId))->values()->all();

        if (!is_numeric($gradeId) || !$schoolIds) {
            return collect();
        }

        return LegacySchoolGradeDiscipline::query()
            ->whereGrade($gradeId)
            ->whereIn('ref_ref_cod_escola', $schoolIds)
            ->selectRaw('ref_ref_cod_escola, ref_cod_disciplina, array_to_json(anos_letivos) AS anos_letivos')
            ->get()
            ->mapWithKeys(function ($vinculo) {
                $chave = $vinculo->ref_ref_cod_escola . '-' . $vinculo->ref_cod_disciplina;

                return [$chave => json_decode($vinculo->anos_letivos, true) ?? []];
            });
    }

    public function delete($gradeId, $schoolId, $disciplineId): bool
    {
        if (!is_numeric($gradeId) || !is_numeric($schoolId) || !is_numeric($disciplineId)) {
            return false;
        }

        if (!LegacyDiscipline::query()->whereKey($disciplineId)->exists()) {
            return false;
        }

        LegacySchoolGradeDiscipline::query()
            ->whereGrade($gradeId)
            ->whereSchool($schoolId)
            ->whereDiscipline($disciplineId)
            ->delete();

        return true;
    }

    public function deleteAll($gradeId, $schoolIds): bool
    {
        $schoolIds = collect($schoolIds)->filter(fn ($schoolId) => is_numeric($schoolId))->values()->all();

        if (!is_numeric($gradeId) || !$schoolIds) {
            return false;
        }

        LegacySchoolGradeDiscipline::query()
            ->whereGrade($gradeId)
            ->whereIn('ref_ref_cod_escola', $schoolIds)
            ->delete();

        return true;
    }

    public function deleteNotIn($gradeId, $schoolId, $keepDisciplines): bool
    {
        if (!is_numeric($gradeId) || !is_numeric($schoolId)) {
            return false;
        }

        $keepDisciplines = collect($keepDisciplines)->filter(fn ($disciplineId) => is_numeric($disciplineId))->values()->all();

        if (!$keepDisciplines) {
            return false;
        }

        LegacySchoolGradeDiscipline::query()
            ->whereGrade($gradeId)
            ->whereSchool($schoolId)
            ->whereNotIn('ref_cod_disciplina', $keepDisciplines)
            ->delete();

        return true;
    }

    public function hasDependency($gradeId, $schoolId, array $disciplines, bool $deleting = false): bool
    {
        if (!is_numeric($gradeId) || !is_numeric($schoolId)) {
            return false;
        }

        $disciplines = array_filter($disciplines, 'is_numeric');

        return LegacySchoolGradeDiscipline::query()
            ->whereGrade($gradeId)
            ->whereSchool($schoolId)
            ->where(
                fn ($query) => $deleting
                    ? $query->whereIn('ref_cod_disciplina', $disciplines)
                    : $query->whereNotIn('ref_cod_disciplina', $disciplines)
            )
            ->whereExists(function ($query) use ($disciplines, $deleting) {
                $query->selectRaw('1')
                    ->from('pmieducar.disciplina_dependencia')
                    ->whereColumn('disciplina_dependencia.ref_cod_escola', 'escola_serie_disciplina.ref_ref_cod_escola')
                    ->whereColumn('disciplina_dependencia.ref_cod_serie', 'escola_serie_disciplina.ref_ref_cod_serie');

                $deleting
                    ? $query->whereIn('disciplina_dependencia.ref_cod_disciplina', $disciplines)
                    : $query->whereNotIn('disciplina_dependencia.ref_cod_disciplina', $disciplines);
            })
            ->exists();
    }

    /**
     * @param int $school
     * @param int $grade
     * @param int $year
     * @return LegacySchoolGradeDiscipline[]|Collection
     */
    public function getDisciplinesForYear($school, $year, $grade = null)
    {
        return LegacyDiscipline::query()
            ->with(['knowledgeArea'])
            ->whereHas('schoolGradeDisciplines', static function ($q) use ($school, $year, $grade) {
                $q->filter([
                    'school' => $school,
                    'yearEq' => $year,
                    'grade' => $grade,
                ]);
                $q->whereHas('school', fn ($q) => $q->active());
                $q->when($grade, fn ($q) => $q->whereHas('grade', fn ($q) => $q->active()));

            })
            ->get();
    }
}
