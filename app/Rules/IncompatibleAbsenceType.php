<?php

namespace App\Rules;

use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacySchoolClass;
use Illuminate\Contracts\Validation\Rule;

class IncompatibleAbsenceType implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return false;
        }

        $schoolClass = $value[0]['turma_id'];
        $schoolClass = LegacySchoolClass::find($schoolClass);
        $grades = array_column($value, 'serie_id');

        $absenceType = LegacyEvaluationRuleGradeYear::query()
            ->whereIn('serie_id', $grades)
            ->where('ano_letivo', $schoolClass->ano)
            ->with('evaluationRule')
            ->get()
            ->map(function ($model) {
                return $model->evaluationRule->tipo_presenca;
            })
            ->toArray();

        return count(array_unique($absenceType)) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'As séries selecionadas devem possuir o mesmo tipo de apuração de presença (geral ou por componente).';
    }
}
