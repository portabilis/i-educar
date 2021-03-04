<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DuplicateMultiGrades implements Rule
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
        $grades = array_column($value, 'serie_id');

        return count($grades) === count(array_unique($grades));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Não é possível adicionar séries iguais em turmas multisseriadas.';
    }
}
