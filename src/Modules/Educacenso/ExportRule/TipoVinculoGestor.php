<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\RegistroEducacenso;

class TipoVinculoGestor implements EducacensoExportRule
{
    /**
     * @param Registro40 $registro40
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro40): RegistroEducacenso
    {
        if ($registro40->situacaoFuncionamento != \iEducar\Modules\Educacenso\Model\SituacaoFuncionamento::EM_ATIVIDADE) {
            $registro40->tipoVinculo = null;
        }

        return $registro40;
    }
}
