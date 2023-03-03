<?php

namespace App\Services\Educacenso\Version2022\Models;

use App\Models\Educacenso\Registro50;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;

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

        for ($index = 9; $index <= count($arrayColumns); $index++) {
            if (!empty($arrayColumns[$index])) {
                $this->componentes[] = $arrayColumns[$index];
            }
        }

        $this->unidadesCurriculares = array_filter([
            isset($arrayColumns[24]) ? UnidadesCurriculares::ELETIVAS : null,
            isset($arrayColumns[25]) ? UnidadesCurriculares::LIBRAS : null,
            isset($arrayColumns[26]) ? UnidadesCurriculares::LINGUA_INDIGENA : null,
            isset($arrayColumns[27]) ? UnidadesCurriculares::LINGUA_LITERATURA_ESTRANGEIRA_ESPANHOL : null,
            isset($arrayColumns[28]) ? UnidadesCurriculares::LINGUA_LITERATURA_ESTRANGEIRA_FRANCES : null,
            isset($arrayColumns[29]) ? UnidadesCurriculares::LINGUA_LITERATURA_ESTRANGEIRA_OUTRA : null,
            isset($arrayColumns[30]) ? UnidadesCurriculares::PROJETO_DE_VIDA : null,
            isset($arrayColumns[31]) ? UnidadesCurriculares::TRILHAS_DE_APROFUNDAMENTO_APRENDIZAGENS : null,
        ]);
    }
}
