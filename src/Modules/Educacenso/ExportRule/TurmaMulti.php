<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;

class TurmaMulti implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        $arrayEtapas = [
            3, 22, 23, 72, 56, 64,
        ];

        if (!in_array($registro60->etapaTurma, $arrayEtapas)) {
            $registro60->etapaAluno = null;
        }

        return $registro60;
    }
}
