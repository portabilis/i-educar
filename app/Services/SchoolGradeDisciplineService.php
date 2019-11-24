<?php

namespace App\Services;

use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Support\Collection;

class SchoolGradeDisciplineService
{
    /**
     * @param int $school
     * @param int $grade
     *
     * @return LegacySchoolGradeDiscipline[]|Collection
     */
    public function getDisciplines($school, $grade)
    {
        return LegacySchoolGradeDiscipline::query()
            ->where('ref_ref_cod_escola', $school)
            ->where('ref_ref_cod_serie', $grade)
            ->get()
            ->pluck('discipline');
    }

    /**
     * @param int $school
     * @param int $grade
     *
     * @return LegacySchoolGradeDiscipline[]|Collection
     */
    public function getAllDisciplines($school, $grade)
    {
        return LegacySchoolGradeDiscipline::query()
            ->where('ref_ref_cod_escola', $school)
            ->where('ref_ref_cod_serie', $grade)
            ->get();
    }
}
