<?php

namespace App\Services;

use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Support\Collection;

class SchoolGradeDisciplineService
{
    /**
     * @param $schoolId
     * @param $gradeId
     * @return LegacySchoolGradeDiscipline[]|Collection
     */
    public function getDisciplines($schoolId, $gradeId)
    {
        return LegacySchoolGradeDiscipline::where('ref_ref_cod_escola', $schoolId)
            ->where('ref_ref_cod_serie', $gradeId)->get()->pluck('discipline');
    }
}
