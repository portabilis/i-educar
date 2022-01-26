<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistsPeriod implements Rule
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
        if (!$value->course->is_standard_calendar) {
            $stage = $value->stages()->first();

            if (!isset($stage->data_inicio)) {
                return false;
            }
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
        return 'Etapas não informadas.';
    }
}
