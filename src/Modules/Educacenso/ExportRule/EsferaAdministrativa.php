<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\Regulamentacao;

class EsferaAdministrativa implements EducacensoExportRule
{
    /**
     * @param Registro00 $registro00
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro00): RegistroEducacenso
    {
        $values = [
            Regulamentacao::SIM,
            Regulamentacao::EM_TRAMITACAO
        ];

        if (in_array($registro00->regulamentacao, $values)) {
            $registro00->esferaFederal = (int) $registro00->esferaFederal;
            $registro00->esferaEstadual = (int) $registro00->esferaEstadual;
            $registro00->esferaMunicipal = (int) $registro00->esferaMunicipal;
        } else {
            $registro00->esferaFederal = null;
            $registro00->esferaEstadual = null;
            $registro00->esferaMunicipal = null;
        }

        return $registro00;
    }
}
