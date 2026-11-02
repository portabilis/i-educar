<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class NonZeroWorkloadRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // 1. Rejeita nulos, strings vazias ou apenas com espaços em branco
        if ($value === null || (is_string($value) && trim($value) === '')) {
            $fail('A carga horária semanal deve ser positiva (> 00:00).');
            return;
        }

        // 2. Rejeita valores zerados explícitos
        if ($value === '00:00' || $value === '0' || $value === '00:00:00') {
            $fail('A carga horária semanal deve ser positiva (> 00:00).');
            return;
        }

        // 3. Validação para formatos de tempo (HH:MM ou com sinal negativo)
        if (is_string($value) && str_contains($value, ':')) {
            // Se tiver sinal de menos em qualquer lugar, já é inválido (negativo)
            if (str_starts_with($value, '-')) {
                $fail('A carga horária semanal deve ser positiva (> 00:00).');
                return;
            }

            $parts = explode(':', $value);
            $hours = (int) ($parts[0] ?? 0);
            $minutes = (int) ($parts[1] ?? 0);
            
            $totalMinutes = ($hours * 60) + $minutes;
            
            if ($totalMinutes <= 0) {
                $fail('A carga horária semanal deve ser positiva (> 00:00).');
            }
            return;
        }

        // 4. Rejeita se não for numérico ou for menor/igual a zero
        if (!is_numeric($value) || (float) $value <= 0) {
            $fail('A carga horária semanal deve ser positiva (> 00:00).');
        }
    }
}