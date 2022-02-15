<?php

namespace App\Rules;

use App\Models\LegacySchoolClass;
use Illuminate\Contracts\Validation\Rule;

class IncompatibleChangeToMultiGrades implements Rule
{
    public function passes($attribute, $value): bool
    {
        $grades = [];
        foreach ($value[1] as $item) {
            $grades[] = $item['serie_id'];
        }

        /** @var LegacySchoolClass $legacySchoolClass */
        $legacySchoolClass = $value[0];

        $changeToMulti = (int) $legacySchoolClass->multiseriada === 1 && $legacySchoolClass->originalMultiGradesInfo === 0;
        $notFindOriginalGrade = ! in_array($legacySchoolClass->originalGrade, $grades);
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
