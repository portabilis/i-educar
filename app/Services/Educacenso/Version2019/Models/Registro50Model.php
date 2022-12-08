<?php

namespace App\Services\Educacenso\Version2019\Models;

use App\Models\Educacenso\Registro50;

class Registro50Model extends Registro50
{
    public function hydrateModel($arrayColumns): void
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->registro = $arrayColumns[1];
        $this->inepEscola = $arrayColumns[2];
        $this->codigoPessoa = $arrayColumns[3];
        $this->inepDocente = $arrayColumns[4];
        $this->codigoTurma = $arrayColumns[5];
        $this->inepTurma = $arrayColumns[6];
        $this->funcaoDocente = $arrayColumns[7] ?: null;
        $this->tipoVinculo = $arrayColumns[8] ?: null;
        $this->componentes = [];

        for ($index = 9; $index <= 23; $index++) {
            if (!empty($arrayColumns[$index])) {
                $this->componentes[] = $arrayColumns[$index];
            }
        }
    }
}
