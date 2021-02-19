<?php

namespace iEducar\Modules\SchoolClass\Validator;

use App\Models\LegacySchoolClass;
use App\Models\LegacyEvaluationRuleGradeYear;

class MultiGradesValidator
{
    private $message;

    public function canSaveSchoolClassGrades($schoolClassGrades)
    {
        if (!$this->compatibleAbsenceType($schoolClassGrades)) {
            $this->message = 'As séries selecionadas devem possuir o mesmo tipo de apuração de presença (geral ou por componente).';

            return false;
        }

        return true;
    }

    public function compatibleAbsenceType($schoolClassGrades)
    {
        $schoolClass = $schoolClassGrades[0]['turma_id'];
        $schoolClass = LegacySchoolClass::find($schoolClass);
        $grades = array_column($schoolClassGrades, 'serie_id');

        $absenceType = LegacyEvaluationRuleGradeYear::query()
            ->whereIn('serie_id', $grades)
            ->where('ano_letivo', $schoolClass->ano)
            ->with('evaluationRule')
            ->get()
            ->map(function ($model) {
                return $model->evaluationRule->tipo_presenca;
            })
            ->toArray();

        return count(array_unique($absenceType)) == 1;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
