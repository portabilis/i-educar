<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SchoolManagerAtLeastOneChief implements Rule
{
    /**
     * Verifica se pelo menos um gestor foi marcado como principal
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (in_array(1, $value)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Informe pelo menos um(a) gestor(a) como gestor(a) principal';
    }
}
