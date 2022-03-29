<?php

namespace App\Rules;

use App\Models\LegacyEnrollment;
use App\Models\LegacySchoolClass;
use Illuminate\Contracts\Validation\Rule;

class CanAlterSchoolClassGrade implements Rule
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
        $isCreate = empty($value->cod_turma);

        if ($isCreate) {
            return true;
        }

        /**
         * Para turmas multisseriadas essa validação
         * é realizada em outro lugar
         */

        if ((bool)$value->multiseriada) {
            return true;
        }

        $oldSchoolClass = LegacySchoolClass::find($value->cod_turma);
        $oldSchoolClassGrade = $oldSchoolClass->ref_ref_cod_serie;
        $newSchoolClassGrade = $value->ref_ref_cod_serie;

        if ((int)$oldSchoolClassGrade === (int)$newSchoolClassGrade) {
            return true;
        }

        $existsEnrollment = LegacyEnrollment::query()
            ->where('ref_cod_turma', $oldSchoolClass->getKey())
            ->where('ativo', 1)
            ->with('registration')
            ->whereHas(
                'registration',
                function ($query) use ($oldSchoolClass) {
                    /** @var Builder $query */
                    $query->where('ref_ref_cod_serie', $oldSchoolClass->ref_ref_cod_serie);
                }
            )
            ->exists();

        if ($existsEnrollment) {
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
        return 'Não é possível alterar a série da turma pois a mesma possui matrículas vinculadas.';
    }
}
