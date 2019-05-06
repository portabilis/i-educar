<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;

class CriterioAcessoGestor implements EducacensoExportRule
{
    /**
     * @param Registro40 $registro40
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro40): RegistroEducacenso
    {
        if ($registro40->criterioAcesso != SchoolManagerAccessCriteria::OUTRO) {
            $registro40->especificacaoCriterioAcesso = null;
        }

        return $registro40;
    }
}
