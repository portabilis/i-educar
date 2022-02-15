<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CanDeleteTurma implements Rule
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
        $enrollments = $value->getActiveEnrollments()->count();
        if ($enrollments > 0) {
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
        return 'A turma possui enturmações ativas.';
    }
}
