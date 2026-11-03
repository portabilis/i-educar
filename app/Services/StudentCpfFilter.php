<?php

namespace App\Services;

class StudentCpfFilter
{
    public static function resolve(?string $raw): array
    {
        $cpf = preg_replace('/\D/', '', (string) $raw);

        if ($cpf === '') {
            return ['cpf' => null, 'error' => null, 'blocked' => false];
        }

        if (!self::isValid($cpf)) {
            return ['cpf' => null, 'error' => 'CPF inválido.', 'blocked' => true];
        }

        return ['cpf' => $cpf, 'error' => null, 'blocked' => false];
    }

    private static function isValid(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $cpf[$i] * (($t + 1) - $i);
            }
            if ((int) $cpf[$t] !== ((10 * $sum) % 11) % 10) {
                return false;
            }
        }

        return true;
    }
}