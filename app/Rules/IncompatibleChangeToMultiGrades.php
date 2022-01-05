<?php

namespace App\Rules;

use App\Models\LegacySchoolClass;
use Illuminate\Contracts\Validation\Rule;

class IncompatibleChangeToMultiGrades implements Rule
{

    public function passes($attribute, $value): bool
    {
        $series = [];
        foreach ($value[1] as $item) {
            $series[] = $item['serie_id'];
        }

        /** @var LegacySchoolClass $legacySchoolClass */
        $legacySchoolClass = $value[0];

        $changeToMulti = $legacySchoolClass->multiseriada === true && $legacySchoolClass->originalMultiGradesInfo === 0;
        $notFindOriginalGrade = ! in_array($legacySchoolClass->originalSerie, $series);
        $containsActiveEnrollments = $legacySchoolClass->getTotalEnrolled() > 0;

        if ($changeToMulti && $notFindOriginalGrade && $containsActiveEnrollments) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
       return 'Não foi possível alterar a turma para ser multisseriada, pois a série original possui matrículas vinculadas.';
    }
}
