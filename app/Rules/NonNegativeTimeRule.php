<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NonNegativeTimeRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_numeric($value) && (float) $value < 0) {
            $fail('A quantidade de horas e minutos não pode ser negativa.');
        } 
    }
}