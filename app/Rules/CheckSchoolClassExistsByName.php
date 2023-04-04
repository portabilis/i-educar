<?php

namespace App\Rules;

use App\Models\LegacySchoolClass;
use Illuminate\Contracts\Validation\Rule;

class CheckSchoolClassExistsByName implements Rule
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
        $name = $value->nm_turma;
        $course = $value->ref_cod_curso;
        $level = $value->ref_cod_serie;
        $school = $value->ref_cod_escola;
        $academicYear = $value->ano_letivo;
        $idToIgnore = $value->id;

        $query = LegacySchoolClass::query()
            ->where('nm_turma', (string) $name)
            ->where('ref_ref_cod_serie', $level)
            ->where('ref_cod_curso', $course)
            ->where('ref_ref_cod_escola', $school)
            ->where('ano', $academicYear)
            ->where('visivel', true)
            ->where('ativo', 1);

        if ($idToIgnore) {
            $query->where('cod_turma', '!=', $idToIgnore);
        }

        $isAvailable = $query->count() === 0;

        return $isAvailable;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O nome da turma já está sendo utilizado nesta escola, para o curso, série e anos informados.';
    }
}
