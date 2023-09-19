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
