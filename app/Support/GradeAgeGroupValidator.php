<?php

namespace App\Support;

class GradeAgeGroupValidator
{
    public static function faixaEtariaEhValida($idadeInicial, $idadeIdeal, $idadeFinal): bool
    {
        $idades = [
            $idadeInicial,
            $idadeIdeal,
            $idadeFinal,
        ];

        foreach ($idades as $idade) {
            if ($idade !== null && $idade !== '' && $idade < 0) {
                return false;
            }
        }
        return true;
    }
}
