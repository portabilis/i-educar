<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EvaluationDecimalPlacesRule implements Rule
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
        if (is_bool($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 0,
                'max_range' => 4,
            ],
        ]) !== false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A quantidade de casas decimais deve ser um número inteiro entre 0 e 4.';
    }
}
