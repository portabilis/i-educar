<?php

namespace App\Rules;

use App\Models\LegacyGrade;
use App\Models\LegacySchoolClass;
use App\Services\SchoolClass\SchoolClassService;
use Illuminate\Contracts\Validation\Rule;

class RequiredAlternativeReportCard implements Rule
{
    private $message;

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
        $year = $schoolClass->ano;
        $grades = array_column($value, 'serie_id');
        $grades = LegacyGrade::query()
            ->whereIn('cod_serie', $grades)
            ->get()
            ->pluck('nm_serie', 'cod_serie')
            ->toArray();

        $schoolClassService = new SchoolClassService();

        $gradesToValidate = [];

        foreach ($value as $key => $dataGrade) {
            if (!empty($dataGrade['boletim_diferenciado_id'])) {
                continue;
            }

            if ($schoolClassService->isRequiredAlternativeReportCard($dataGrade['serie_id'], $year)) {
                $gradesToValidate[] = $grades[$dataGrade['serie_id']];
            }
        }

        if (count($gradesToValidate) > 0) {
            $gradesToValidate = implode(', ', $gradesToValidate);
            $this->message = "O campo '<b>Boletim diferenciado</b>' é obrigatório para a(s) série(s): $gradesToValidate, pois a regra de avaliação possui regra diferenciada definida.";

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
