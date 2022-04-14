<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;

class PoderPublicoResponsavelTransporte implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        if ($registro60->transportePublico == 0) {
            $registro60->poderPublicoResponsavelTransporte = null;
        }

        return $registro60;
    }
}
