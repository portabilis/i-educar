<?php

namespace App\Services;

class AntropometriaService
{
    public static function calculaIMC(float $pesoKg, float $alturaCm): float
    {
        $alturaM = $alturaCm / 100;
        return $pesoKg / ($alturaM * $alturaM);
    }

    public static function classificaIMC(float $zScore): string
    {
        return match (true) {
            $zScore < -3 => 'Magreza acentuada',
            $zScore >= -3 && $zScore < -2 => 'Magreza',
            $zScore >= -2 && $zScore <= 1 => 'Eutrofia (peso adequado)',
            $zScore > 1 && $zScore <= 2 => 'Sobrepeso',
            $zScore > 2 && $zScore <= 3 => 'Obesidade',
            $zScore > 3 => 'Obesidade grave',
            default => 'Indefinido',
        };
    }

    public static function classificaCintura(string $sexo, float $cintura): string
    {
        if ($sexo === 'M') {
            if ($cintura >= 102) return 'Risco muito aumentado';
            if ($cintura >= 94) return 'Risco aumentado';
        }

        if ($sexo === 'F') {
            if ($cintura >= 88) return 'Risco muito aumentado';
            if ($cintura >= 80) return 'Risco aumentado';
        }

        return 'Sem risco identificado';
    }
}
