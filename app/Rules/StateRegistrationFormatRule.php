<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StateRegistrationFormatRule implements Rule
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
        return preg_match('/[0-9]{3}\.[0-9]{3}\.[0-9]{3}(\-[0-9]{1})?/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O número de inscrição é inválido.';
    }
}
