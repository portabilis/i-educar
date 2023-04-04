<?php

namespace App\Rules;

use App\Models\LegacyEnrollment;
use Illuminate\Contracts\Validation\Rule;

class ExistsEnrollmentsInSchoolClassGrades implements Rule
{
    private $gradesWithEnrollments;

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
        $schoolClass = $value['turma'];
        $gradesToDelete =  $value['grades_delete'];

        $gradesWithEnrollments = LegacyEnrollment::query()
            ->where('ref_cod_turma', $schoolClass->getKey())
            ->where('ativo', 1)
            ->with('registration')
            ->whereHas(
                'registration',
                function ($query) use ($schoolClass, $gradesToDelete) {
                    /** @var Builder $query */
                    $query->where('ano', $schoolClass->ano);
                    $query->whereIn('ref_ref_cod_serie', $gradesToDelete);
                }
            )
            ->get()
            ->pluck('registration.grade.nm_serie', 'registration.grade.cod_serie')
            ->toArray();

        $this->gradesWithEnrollments = implode(', ', $gradesWithEnrollments);

        return count($gradesWithEnrollments) === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Não é possível remover a(s) série(s): ' . $this->gradesWithEnrollments . ', pois existem matrículas vinculadas.';
    }
}
