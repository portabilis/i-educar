<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class WorkloadLimitRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (is_string($value) && str_contains($value, ':')) {
            $parts = explode(':', $value);
            $intHours = (int) ($parts[0] ?? 0);
            $intMinutes = (int) ($parts[1] ?? 0);

            if ($intHours > 99 || ($intHours === 99 && $intMinutes > 59)) {
                $fail('O limite superior estrito de carga horária é 99:59 em formato HH:MM.');
            }
        }
    }
}