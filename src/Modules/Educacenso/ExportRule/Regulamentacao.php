<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\Regulamentacao as RegulamentacaoModel;

class Regulamentacao implements EducacensoExportRule
{
    /**
     * @param Registro00 $registro00
     */
    public static function handle(RegistroEducacenso $registro00): RegistroEducacenso
    {
        $valoresPermitidos = [RegulamentacaoModel::SIM, RegulamentacaoModel::EM_TRAMITACAO];

        if (!in_array($registro00->regulamentacao, $valoresPermitidos)) {
            $registro00->esferaAdministrativa = null;
        }

        return $registro00;
    }
}
