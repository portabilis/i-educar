<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CourseWorkloadRule implements Rule
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
        if (is_bool($value) || is_null($value)) {
            return false;
        }

        return is_numeric($value) && (float) $value > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A carga horária do curso deve ser um valor numérico maior que zero.';
    }
}
