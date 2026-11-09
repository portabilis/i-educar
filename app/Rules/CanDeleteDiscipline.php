<?php

namespace App\Rules;

use App\Models\LegacyDisciplineAcademicYear;
use Illuminate\Contracts\Validation\Rule;

class CanDeleteDiscipline implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return LegacyDisciplineAcademicYear::query()
            ->where('componente_curricular_id', $value->getKey())
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Este componente está vinculado a uma ou mais séries e não pode ser excluído para preservar o histórico.';
    }
}
