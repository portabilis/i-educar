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

        if (!in_array($registro00->regulamentacao, $values)) {
            $registro00->esferaAdministrativa = null;
        }

        return $registro00;
    }
}
