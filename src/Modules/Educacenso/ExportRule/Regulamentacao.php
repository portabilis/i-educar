<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\Regulamentacao as RegulamentacaoModel;

class Regulamentacao implements EducacensoExportRule
{
    /**
     * @param Registro00 $registro00
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro00): RegistroEducacenso
    {
        if ($registro00->regulamentacao == RegulamentacaoModel::NAO) {
            $registro00->esferaFederal = null;
            $registro00->esferaEstadual = null;
            $registro00->esferaMunicipal = null;
        }

        if ($registro00->esferaFederal == 0) {
            $registro00->esferaFederal = null;
        }

        if ($registro00->esferaEstadual == 0) {
            $registro00->esferaEstadual = null;
        }

        if ($registro00->esferaMunicipal == 0) {
            $registro00->esferaMunicipal = null;
        }

        return $registro00;
    }
}
