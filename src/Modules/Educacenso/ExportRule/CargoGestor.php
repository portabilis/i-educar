<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;

class CargoGestor implements EducacensoExportRule
{
    /**
     * @param Registro40 $registro40
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro40): RegistroEducacenso
    {
        if ($registro40->cargo == SchoolManagerRole::OUTRO) {
            $registro40->criterioAcesso = null;
            $registro40->tipoVinculo = null;
        }

        if ($registro40->dependenciaAdministrativa == DependenciaAdministrativaEscola::PRIVADA) {
            $registro40->tipoVinculo = null;
        }

        return $registro40;
    }
}
